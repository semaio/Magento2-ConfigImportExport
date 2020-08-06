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
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;

/**
 * Class ImportProcessor
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
class ImportProcessor extends AbstractProcessor implements ImportProcessorInterface
{
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
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var ScopeConverterInterface
     */
    private $scopeConverter;

    /**
     * @param WriterInterface $configWriter
     * @param ScopeValidatorInterface $scopeValidator
     * @param ScopeConverterInterface $scopeConverter
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeValidatorInterface $scopeValidator,
        ScopeConverterInterface $scopeConverter
    ) {
        $this->configWriter = $configWriter;
        $this->scopeValidator = $scopeValidator;
        $this->scopeConverter = $scopeConverter;
    }

    /**
     * Process the import
     */
    public function process()
    {
        // Find files
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
     * @param string $file
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
     * @param ReaderInterface $reader
     */
    public function setReader(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param FinderInterface $finder
     */
    public function setFinder(FinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param string $path
     * @param array  $config
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
                    $errorMsg = sprintf(
                        '<error>ERROR: Invalid scopeId "%s" for scope "%s" (%s => %s)</error>',
                        $scopeId,
                        $scope,
                        $path,
                        $value
                    );
                    $this->getOutput()->writeln($errorMsg);
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
