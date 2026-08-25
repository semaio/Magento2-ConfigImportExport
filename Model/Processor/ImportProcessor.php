<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Magento\Config\App\Config\Type\System;
use Magento\Framework\App\Config\ConfigPathResolver;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Stdlib\ArrayManager;
use Semaio\ConfigImportExport\Exception\UnresolveableValueException;
use Semaio\ConfigImportExport\Model\Converter\ScopeConverterInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;
use Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface;
use Semaio\ConfigImportExport\Model\Resolver\ResolverInterface;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;

class ImportProcessor extends AbstractProcessor implements ImportProcessorInterface
{
    private const DELETE_CONFIG_FLAG = '!!DELETE';
    private const KEEP_CONFIG_FLAG = '!!KEEP';
    private const IF_MODIFIER = 'if';
    private const IF_MODIFIER_NOT_SET = 'not-set';

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ScopeValidatorInterface
     */
    private $scopeValidator;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var ScopeConverterInterface
     */
    private $scopeConverter;

    /**
     * @var ResolverInterface[]
     */
    private $resolvers;

    /**
     * @var DeploymentConfigWriter
     */
    private $deploymentConfigWriter;

    /**
     * @var ConfigPathResolver
     */
    private $configPathResolver;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Config paths flagged with "if: not-set" that must only be imported when no value exists yet.
     *
     * @var array<string, bool>
     */
    private $ifNotSetPaths = [];

    /**
     * @param WriterInterface         $configWriter
     * @param ScopeValidatorInterface $scopeValidator
     * @param ScopeConverterInterface $scopeConverter
     * @param DeploymentConfigWriter  $deploymentConfigWriter
     * @param ConfigPathResolver      $configPathResolver
     * @param ArrayManager            $arrayManager
     * @param ResourceConnection      $resourceConnection
     * @param ResolverInterface[]     $resolvers
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeValidatorInterface $scopeValidator,
        ScopeConverterInterface $scopeConverter,
        DeploymentConfigWriter $deploymentConfigWriter,
        ConfigPathResolver $configPathResolver,
        ArrayManager $arrayManager,
        ResourceConnection $resourceConnection,
        array $resolvers = []
    ) {
        $this->configWriter = $configWriter;
        $this->scopeValidator = $scopeValidator;
        $this->scopeConverter = $scopeConverter;
        $this->deploymentConfigWriter = $deploymentConfigWriter;
        $this->configPathResolver = $configPathResolver;
        $this->arrayManager = $arrayManager;
        $this->resourceConnection = $resourceConnection;
        $this->resolvers = $resolvers;
    }

    /**
     * Process configuration import.
     *
     * @return void
     */
    public function process()
    {
        $files = $this->finder->find();

        if (0 === count($files)) {
            if (false === $this->getInput()->getOption('allow-empty-directories')) {
                throw new \InvalidArgumentException('No files found for format: *.' . $this->getFormat());
            } else {
                $this->getOutput()->writeln('No files found for format: *.' . $this->getFormat());
                $this->getOutput()->writeln('Maybe this is expected behaviour, because you passed the --allow-empty-directories option.');
                $this->getOutput()->writeln(' ');
            }
        }

        $configurationValues = $this->collectConfigurationValues($files);
        if (0 === count($configurationValues)) {
            return;
        }

        $shouldLock = (bool) $this->getInput()->getOption('lock');
        $lockData = [];

        foreach ($configurationValues as $configPath => $configValue) {
            foreach ($configValue as $scopeType => $scopeValue) {
                foreach ($scopeValue as $scopeId => $value) {
                    if ($value === self::DELETE_CONFIG_FLAG) {
                        $this->configWriter->delete($configPath, $scopeType, $scopeId);
                        $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, 'DELETED'));

                        if ($shouldLock) {
                            $this->getOutput()->writeln(sprintf('<info>  Note: If this path is locked in app/etc/config.php, remove it manually.</info>'));
                        }

                        continue;
                    }

                    if ($value === self::KEEP_CONFIG_FLAG) {
                        $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, 'KEPT'));

                        continue;
                    }

                    if (!empty($this->ifNotSetPaths[$configPath]) && $this->isConfigValueSet($configPath, $scopeType, $scopeId)) {
                        $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, 'SKIPPED (already set)'));

                        continue;
                    }

                    $this->configWriter->save($configPath, $value, $scopeType, $scopeId);
                    $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, $value));

                    if ($shouldLock) {
                        $lockData[] = [
                            'path'     => $configPath,
                            'value'    => $value,
                            'scope'    => $scopeType,
                            'scope_id' => $scopeId,
                        ];
                    }
                }
            }
        }

        if ($shouldLock && !empty($lockData)) {
            $this->writeToConfigPhp($lockData);
        }
    }

    /**
     * Check whether a value for the given path/scope is already stored in core_config_data.
     *
     * Deliberately checks for an existing database row instead of using ScopeConfigInterface,
     * so that config.xml defaults and app/etc/config.php values do not count as "set".
     *
     * @param string     $configPath
     * @param string     $scopeType
     * @param int|string $scopeId
     */
    private function isConfigValueSet(string $configPath, string $scopeType, $scopeId): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('core_config_data'), 'config_id')
            ->where('path = ?', $configPath)
            ->where('scope = ?', $scopeType)
            ->where('scope_id = ?', $scopeId);

        return false !== $connection->fetchOne($select);
    }

    /**
     * Batch-write collected values to app/etc/config.php.
     */
    private function writeToConfigPhp(array $lockData): void
    {
        $this->getOutput()->writeln(' ');
        $this->getOutput()->writeln('<info>Locking values in app/etc/config.php...</info>');

        $configArray = [];
        foreach ($lockData as $item) {
            $resolvedPath = $this->configPathResolver->resolve(
                $item['path'],
                $item['scope'],
                $item['scope_id'],
                System::CONFIG_TYPE
            );
            $configArray = $this->arrayManager->set($resolvedPath, $configArray, $item['value']);
        }

        $this->deploymentConfigWriter->saveConfig(
            [ConfigFilePool::APP_CONFIG => $configArray],
            false
        );

        $this->getOutput()->writeln(sprintf(
            '<info>%d %s locked in app/etc/config.php.</info>',
            count($lockData),
            count($lockData) === 1 ? 'value' : 'values'
        ));
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function collectConfigurationValues(array $files): array
    {
        $buffer = [];
        $this->ifNotSetPaths = [];

        foreach ($files as $file) {
            $valuesSet = 0;

            $configurations = $this->getConfigurationsFromFile($file);
            foreach ($configurations as $configPath => $configValues) {
                if (!isset($buffer[$configPath])) {
                    $buffer[$configPath] = [];
                }

                // A file that re-declares the path without the modifier overrides it, like any other value.
                $this->ifNotSetPaths[$configPath] = false;
                if (is_array($configValues) && array_key_exists(self::IF_MODIFIER, $configValues)) {
                    $condition = $configValues[self::IF_MODIFIER];
                    unset($configValues[self::IF_MODIFIER]);

                    if ($condition !== self::IF_MODIFIER_NOT_SET) {
                        $this->getOutput()->writeln(sprintf(
                            '<error>ERROR: Invalid "if" condition "%s" for path "%s". Only "%s" is supported.</error>',
                            is_scalar($condition) ? $condition : gettype($condition),
                            $configPath,
                            self::IF_MODIFIER_NOT_SET
                        ));

                        continue;
                    }

                    $this->ifNotSetPaths[$configPath] = true;
                }

                $scopeConfigValues = $this->transformConfigToScopeConfig($configPath, $configValues);
                foreach ($scopeConfigValues as $scopeConfigValue) {
                    $scopeType = $scopeConfigValue['scope'];
                    $scopeId = $this->scopeConverter->convert($scopeConfigValue['scope_id'], $scopeConfigValue['scope']);
                    $buffer[$configPath][$scopeType][$scopeId] = $scopeConfigValue['value'];
                    $valuesSet++;
                }
            }

            if (0 === $valuesSet) {
                continue;
            }

            $this->getOutput()->writeln(sprintf(
                '<info>Collected configuration values from %s with %s %s.</info>',
                $file,
                $valuesSet,
                $valuesSet === 1 ? 'value' : 'values'
            ));
        }

        $this->getOutput()->writeln(' '); // Add empty line to make output in terminal nicer.

        return $buffer;
    }

    /**
     * @param InputInterface $input
     *
     * @return void
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param QuestionHelper $questionHelper
     *
     * @return void
     */
    public function setQuestionHelper(QuestionHelper $questionHelper)
    {
        $this->questionHelper = $questionHelper;
    }

    /**
     * @return QuestionHelper
     */
    public function getQuestionHelper()
    {
        return $this->questionHelper;
    }

    /**
     * @param ReaderInterface $reader
     *
     * @return void
     */
    public function setReader(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param FinderInterface $finder
     *
     * @return void
     */
    public function setFinder(FinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function getConfigurationsFromFile($file)
    {
        $configurations = $this->reader->parse($file);
        if (!is_array($configurations)) {
            $this->getOutput()->writeln(
                sprintf("<error>Skipped: '%s' (not an array: %s).</error>", $file, var_export($configurations, true))
            );
            $configurations = [];
        }

        return $configurations;
    }

    /**
     * @param string $path
     * @param array  $config
     *
     * @return array
     */
    public function transformConfigToScopeConfig($path, array $config)
    {
        $return = [];

        foreach ($config as $scope => $scopeIdValue) {
            if (!$scopeIdValue) {
                continue;
            }

            foreach ($scopeIdValue as $scopeId => $value) {
                if (!$this->scopeValidator->validate($scope, $scopeId)) {
                    $this->getOutput()->writeln(sprintf(
                        '<error>ERROR: Invalid scopeId "%s" for scope "%s" (%s => %s)</error>',
                        $scopeId,
                        $scope,
                        $path,
                        $value
                    ));

                    continue;
                }

                try {
                    foreach ($this->resolvers as $resolver) {
                        if (!$resolver->supports($value, $path)) {
                            continue;
                        }

                        $resolver->setInput($this->getInput());
                        $resolver->setOutput($this->getOutput());
                        $resolver->setQuestionHelper($this->getQuestionHelper());

                        $value = $resolver->resolve($value, $path);
                    }
                } catch (UnresolveableValueException $exception) {
                    $this->getOutput()->writeln(sprintf(
                        '<error>%s (%s => %s)</error>',
                        $exception->getMessage(),
                        $path,
                        $value
                    ));

                    continue;
                }

                $return[] = [
                    'value'    => $value,
                    'scope'    => $scope,
                    'scope_id' => $scopeId,
                ];
            }
        }

        return $return;
    }
}
