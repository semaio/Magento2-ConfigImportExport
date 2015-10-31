<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Processor;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;

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
     * @var array
     */
    private $readers;

    /**
     * @var string
     */
    private $folder;

    /**
     * @var array
     */
    private $environment;
    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @param WriterInterface         $configWriter
     * @param ScopeValidatorInterface $scopeValidator
     * @param FinderInterface         $finder
     * @param array                   $readers
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeValidatorInterface $scopeValidator,
        FinderInterface $finder,
        array $readers = []
    )
    {
        $this->configWriter = $configWriter;
        $this->scopeValidator = $scopeValidator;
        $this->finder = $finder;
        $this->readers = $readers;
    }

    /**
     * Process the import
     */
    public function process()
    {
        $this->writeSection('Start Import');

        // Check if there is a reader for the given file extension
        $format = $this->getFormat();
        if (!array_key_exists($format, $this->readers)) {
            throw new \InvalidArgumentException('Format "' . $format . '" is currently not supported."');
        }

        /** @var \Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface $reader */
        $reader = new $this->readers[$format];
        if (!$reader || !is_object($reader)) {
            throw new \InvalidArgumentException(ucfirst($format) . ' file reader could not be instantiated."');
        }

        // Find files
        $finder = $this->finder;
        $finder->setEnvironment($this->environment);
        $finder->setBaseFolder($this->getBaseFolderName());
        $finder->setFolder($this->folder);
        $finder->setFormat($this->getFormat());
        $files = $finder->find();
        if (0 === count($files)) {
            throw new \InvalidArgumentException('No files found for format: *.' . $format);
        }

        foreach ($files as $file) {
            $valuesSet = 0;
            $configurations = $reader->parse($file);
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
     * @param string $folder
     * @throws \InvalidArgumentException
     */
    public function setFolder($folder)
    {
        if (false === is_dir($folder) || false === is_readable($folder)) {
            throw new \InvalidArgumentException('Cannot access folder: ' . $folder);
        }
        $this->folder = rtrim($folder, '/');
    }

    /**
     * @param string $environment
     * @throws \InvalidArgumentException
     */
    public function setEnvironment($environment)
    {
        $environmentFolder = $this->folder . DIRECTORY_SEPARATOR . $environment;
        if (false === is_dir($environmentFolder) || false === is_readable($environmentFolder)) {
            throw new \InvalidArgumentException('Cannot access folders for environment: ' . $environment);
        }
        $this->environment = explode(DIRECTORY_SEPARATOR, trim($environment, DIRECTORY_SEPARATOR));
    }

    /**
     * @return string
     */
    public function getBaseFolderName()
    {
        return $this->getInput()->getOption('base');
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

                // Valid scope Write output
                $value = str_replace("\r", '', addcslashes($value, '"'));
                $value = str_replace("\n", '\\n', $value);

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
