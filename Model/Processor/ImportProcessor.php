<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Processor;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;
use Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface;

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
     * @param WriterInterface         $configWriter
     * @param ScopeValidatorInterface $scopeValidator
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeValidatorInterface $scopeValidator
    ) {
        $this->configWriter = $configWriter;
        $this->scopeValidator = $scopeValidator;
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
            $configurations = $this->reader->parse($file);
            foreach ($configurations as $configPath => $configValues) {
                $scopeConfigValues = $this->transformConfigToScopeConfig($configPath, $configValues);
                foreach ($scopeConfigValues as $scopeConfigValue) {
                    $this->configWriter->save(
                        $configPath,
                        $scopeConfigValue['value'],
                        $scopeConfigValue['scope'],
                        $scopeConfigValue['scope_id']
                    );

                    $this->getOutput()->writeln(sprintf('<comment>%s => %s</comment>', $configPath, $scopeConfigValue['value']));
                    $valuesSet++;
                }
            }

            $this->getOutput()->writeln(sprintf('<info>Processed: %s with %s value(s).</info>', $file, $valuesSet));
        }
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
            foreach ($scopeIdValue as $scopeId => $value) {
                $scopeId = (int)$scopeId;

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
