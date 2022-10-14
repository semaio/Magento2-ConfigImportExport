<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Semaio\ConfigImportExport\Model\Converter\ScopeConverterInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;
use Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface;
use Semaio\ConfigImportExport\Model\Resolver\EnvironmentVariableResolver;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;

class ImportProcessor extends AbstractProcessor implements ImportProcessorInterface
{
    private const DELETE_CONFIG_FLAG = '!!DELETE';
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
     * @var EnvironmentVariableResolver
     */
    private $environmentVariableResolver;

    /**
     * @param WriterInterface             $configWriter
     * @param ScopeValidatorInterface     $scopeValidator
     * @param ScopeConverterInterface     $scopeConverter
     * @param EnvironmentVariableResolver $environmentVariableResolver
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeValidatorInterface $scopeValidator,
        ScopeConverterInterface $scopeConverter,
        EnvironmentVariableResolver $environmentVariableResolver
    ) {
        $this->configWriter = $configWriter;
        $this->scopeValidator = $scopeValidator;
        $this->scopeConverter = $scopeConverter;
        $this->environmentVariableResolver = $environmentVariableResolver;
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
            throw new \InvalidArgumentException('No files found for format: *.' . $this->getFormat());
        }

        foreach ($files as $file) {
            $valuesSet = 0;
            $configurations = $this->getConfigurationsFromFile($file);
            foreach ($configurations as $configPath => $configValues) {
                $scopeConfigValues = $this->transformConfigToScopeConfig($configPath, $configValues);
                foreach ($scopeConfigValues as $scopeConfigValue) {
                    if ($scopeConfigValue['value'] === self::DELETE_CONFIG_FLAG) {
                        $this->configWriter->delete(
                            $configPath,
                            $scopeConfigValue['scope'],
                            $this->scopeConverter->convert($scopeConfigValue['scope_id'], $scopeConfigValue['scope'])
                        );

                        $this->getOutput()->writeln(sprintf('<comment>%s => %s</comment>', $configPath, 'DELETED'));
                        $valuesSet++;

                        continue;
                    }

                    $this->configWriter->save(
                        $configPath,
                        $scopeConfigValue['value'],
                        $scopeConfigValue['scope'],
                        $this->scopeConverter->convert($scopeConfigValue['scope_id'], $scopeConfigValue['scope'])
                    );

                    $this->getOutput()->writeln(sprintf('<comment>%s => %s</comment>', $configPath, $scopeConfigValue['value']));
                    $valuesSet++;
                }
            }

            $this->getOutput()->writeln(sprintf('<info>Processed: %s with %s value(s).</info>', $file, $valuesSet));
        }
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
                    $value = $this->environmentVariableResolver->resolveValue($value);
                } catch (\UnexpectedValueException $e) {
                    if ($this->getInput()->getOption('prompt-missing-env-vars') && $this->getInput()->isInteractive()) {
                        $value = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), new Question($path . ': '));
                    } else {
                        $this->getOutput()->writeln(sprintf(
                            '<error>%s (%s => %s)</error>',
                            $e->getMessage(),
                            $path,
                            $value
                        ));

                        continue;
                    }
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
