<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Semaio\ConfigImportExport\Model\Processor\ExportProcessorInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;

/**
 * Class ExportCommand
 *
 * @package Semaio\ConfigImportExport\Command
 */
class ExportCommand extends AbstractCommand
{
    /**
     * Command Name
     */
    const COMMAND_NAME = 'config:data:export';

    /**
     * @var ExportProcessorInterface
     */
    private $exportProcessor;

    /**
     * @param Registry                 $registry
     * @param AppState                 $appState
     * @param ConfigLoader             $configLoader
     * @param ObjectManagerInterface   $objectManager
     * @param CacheManager             $cacheManager
     * @param ExportProcessorInterface $exportProcessor
     * @param null                     $name
     */
    public function __construct(
        Registry $registry,
        AppState $appState,
        ConfigLoader $configLoader,
        ObjectManagerInterface $objectManager,
        CacheManager $cacheManager,
        ExportProcessorInterface $exportProcessor,
        $name = null
    ) {
        $this->exportProcessor = $exportProcessor;
        parent::__construct($registry, $appState, $configLoader, $objectManager, $cacheManager, $name);
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Export settings from "core_config_data" into a file');
        $this->addOption('format', 'm', InputOption::VALUE_OPTIONAL, 'Format: yaml', 'yaml');
        $this->addOption('hierarchical', 'a', InputOption::VALUE_OPTIONAL, 'Create a hierarchical or a flat structure (not all export format supports that). Enable with: y', 'n');
        $this->addOption('filename', 'f', InputOption::VALUE_OPTIONAL, 'File name into which should the export be written. Defaults into var directory.');
        $this->addOption('include', 'i', InputOption::VALUE_OPTIONAL, 'Path prefix, multiple values can be comma separated; exports only those paths');
        $this->addOption('includeScope', null, InputOption::VALUE_OPTIONAL, 'Scope name, multiple values can be comma separated; exports only those scopes');
        $this->addOption('exclude', 'x', InputOption::VALUE_OPTIONAL, 'Path prefix, multiple values can be comma separated; exports everything except ...');
        $this->addOption('filePerNameSpace', 's', InputOption::VALUE_OPTIONAL, 'Export each namespace into its own file. Enable with: y', 'n');
        $this->addOption('exclude-default', 'c', InputOption::VALUE_OPTIONAL, 'Excludes default values (@todo)');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->writeSection('Start Export');

        $this->exportProcessor->setOutput($output);
        $this->exportProcessor->process();
    }
}

