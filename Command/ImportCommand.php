<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Command;

use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Semaio\ConfigImportExport\Model\File\FinderInterface;
use Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface;
use Semaio\ConfigImportExport\Model\Processor\ImportProcessorInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends AbstractCommand
{
    /**
     * Command Name
     */
    const COMMAND_NAME = 'config:data:import';

    /**
     * @var ImportProcessorInterface
     */
    private $importProcessor;

    /**
     * @var array
     */
    private $readers;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @param Registry                 $registry
     * @param AppState                 $appState
     * @param ObjectManagerInterface   $objectManager
     * @param CacheManager             $cacheManager
     * @param ImportProcessorInterface $importProcessor
     * @param FinderInterface          $finder
     * @param array                    $readers
     * @param null                     $name
     */
    public function __construct(
        Registry $registry,
        AppState $appState,
        ObjectManagerInterface $objectManager,
        CacheManager $cacheManager,
        ImportProcessorInterface $importProcessor,
        FinderInterface $finder,
        array $readers = [],
        $name = null
    ) {
        $this->importProcessor = $importProcessor;
        $this->readers = $readers;
        $this->finder = $finder;

        parent::__construct($registry, $appState, $objectManager, $cacheManager, $name);
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Import "core_config_data" settings for an environment');

        $this->addArgument(
            'folder',
            InputArgument::REQUIRED,
            'Import folder name'
        );

        $this->addArgument(
            'environment',
            InputArgument::REQUIRED,
            'Environment name. SubEnvs separated by slash e.g.: development/osx/developer01'
        );

        $this->addOption(
            'base',
            'b',
            InputOption::VALUE_OPTIONAL,
            'Base folder name',
            'base'
        );

        $this->addOption(
            'format',
            'm',
            InputOption::VALUE_OPTIONAL,
            'Format: yaml, json (Default: yaml)',
            'yaml'
        );

        $this->addOption(
            'no-cache',
            null,
            InputOption::VALUE_NONE,
            'Do not clear cache after config data import.'
        );

        $this->addOption(
            'recursive',
            'r',
            InputOption::VALUE_NONE,
            'Recursively go over subdirectories and import configs.'
        );

        $this->addOption(
            'prompt-missing-env-vars',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Prompt for missing env vars input.',
            true
        );

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->writeSection('Start Import');

        // Check if there is a reader for the given file extension
        $format = $this->getFormat();
        if (!array_key_exists($format, $this->readers)) {
            throw new \InvalidArgumentException('Format "' . $format . '" is currently not supported."');
        }

        /** @var ReaderInterface $reader */
        $reader = $this->getObjectManager()->create($this->readers[$format]);
        if (!is_object($reader)) {
            throw new \InvalidArgumentException(ucfirst($format) . ' file reader could not be instantiated."');
        }

        // Retrieve the arguments
        $folder = $input->getArgument('folder');
        $baseFolder = $input->getOption('base');
        $environment = $input->getArgument('environment');
        $depth = ($input->getOption('recursive') === false) ? '0' : '>= 0';

        // Configure the finder
        $finder = $this->finder;
        $finder->setFolder($folder);
        $finder->setBaseFolder($baseFolder);
        $finder->setEnvironment($environment);
        $finder->setFormat($format);
        $finder->setDepth($depth);

        // Process the import
        $this->importProcessor->setFormat($format);
        $this->importProcessor->setReader($reader);
        $this->importProcessor->setFinder($finder);
        $this->importProcessor->setInput($input);
        $this->importProcessor->setOutput($output);
        $this->importProcessor->setQuestionHelper($this->getHelper('question'));
        $this->importProcessor->process();

        // Clear the cache after import
        if ($input->getOption('no-cache') === false) {
            $this->writeSection('Clear cache');

            $this->getCacheManager()->clean(['config', 'full_page']);

            $output->writeln(sprintf('<info>Cache cleared.</info>'));
        }

        return 0;
    }
}
