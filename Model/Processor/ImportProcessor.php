<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Magento\Framework\App\Config\Storage\WriterInterface;
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
     * @param WriterInterface         $configWriter
     * @param ScopeValidatorInterface $scopeValidator
     * @param ScopeConverterInterface $scopeConverter
     * @param ResolverInterface[]     $resolvers
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeValidatorInterface $scopeValidator,
        ScopeConverterInterface $scopeConverter,
        array $resolvers = []
    ) {
        $this->configWriter = $configWriter;
        $this->scopeValidator = $scopeValidator;
        $this->scopeConverter = $scopeConverter;
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
        if (0 === count($files) && false === $this->getInput()->getOption('allow-empty-directories')) {
            throw new \InvalidArgumentException('No files found for format: *.' . $this->getFormat());
        } else {
            $this->getOutput()->writeln('No files found for format: *.' . $this->getFormat());
            $this->getOutput()->writeln('Maybe this is expected behaviour, because you passed the --allow-empty-directories option.');
        }

        $configs = $this->collectConfigs($files);

        foreach ($configs as $configPath => $configValue) {
            foreach ($configValue as $scopeType => $scopeValue) {
                foreach ($scopeValue as $scopeId => $value) {

                    if ($value === self::DELETE_CONFIG_FLAG) {
                        $this->configWriter->delete($configPath, $scopeType, $scopeId);
                        $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, 'DELETED'));
                        continue;
                    }

                    if ($value === self::KEEP_CONFIG_FLAG) {
                        $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, 'KEPT'));
                        continue;
                    }

                    $this->configWriter->save($configPath, $value, $scopeType, $scopeId);
                    $this->getOutput()->writeln(sprintf('<comment>[%s] [%s] %s => %s</comment>', $scopeType, $scopeId, $configPath, $value));
                }
            }
        }
    }

    /**
     * @param array $files
     * @return array
     */
    private function collectConfigs(array $files): array
    {
        $buffer = [];

        foreach ($files as $file) {
            $valuesSet = 0;
            $configurations = $this->getConfigurationsFromFile($file);

            foreach ($configurations as $configPath => $configValues) {

                if (!isset($buffer[$configPath])) {
                    $buffer[$configPath] = [];
                }

                $scopeConfigValues = $this->transformConfigToScopeConfig($configPath, $configValues);

                foreach ($scopeConfigValues as $scopeConfigValue) {

                    $scopeType = $scopeConfigValue['scope'];
                    $scopeId = $this->scopeConverter->convert($scopeConfigValue['scope_id'], $scopeConfigValue['scope']);
                    $buffer[$configPath][$scopeType][$scopeId] = $scopeConfigValue['value'];
                    $valuesSet++;
                }
            }

            $this->getOutput()->writeln(sprintf('<info>Configs collected: %s with %s value(s).</info>', $file, $valuesSet));
        }

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
