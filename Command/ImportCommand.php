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

/**
 * Class ImportCommand
 *
 * @package Semaio\ConfigImportExport\Command
 */
class ImportCommand extends AbstractCommand
{
    /**
     * Command Name
     */
    const COMMAND_NAME = 'config:data:import';

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Import "core_config_data" settings for an environment');
        $this->addArgument('folder', InputArgument::REQUIRED, 'Import folder name');
        $this->addArgument('environment', InputArgument::REQUIRED, 'Environment name. SubEnvs separated by slash e.g.: development/osx/developer01');
        $this->addOption('base', null, InputOption::VALUE_OPTIONAL, 'Base folder name', 'base');
        $this->addOption('format', 'm', InputOption::VALUE_OPTIONAL, 'Format: yaml, json (Default: yaml)', 'yaml');

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

        // Set the import folder
        $folder = $input->getArgument('folder');
        $this->_importProcessor->setFolder($folder);

        // Set the environment
        $environment = $input->getArgument('environment');
        $this->_importProcessor->setEnvironment($environment);

        // Process the import
        $this->_importProcessor->setInput($input);
        $this->_importProcessor->setOutput($output);
        $this->_importProcessor->process();

        // Clear the cache after import
        $this->getCacheManager()->clean(['config', 'full_page']);
    }
}

