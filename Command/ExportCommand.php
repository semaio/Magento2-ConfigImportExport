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
use Semaio\ConfigImportExport\Model\Processor\ExportProcessorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @var array
     */
    private $writers;

    /**
     * @param Registry                 $registry
     * @param AppState                 $appState
     * @param ObjectManagerInterface   $objectManager
     * @param CacheManager             $cacheManager
     * @param ExportProcessorInterface $exportProcessor
     * @param array                    $writers
     * @param null                     $name
     */
    public function __construct(
        Registry $registry,
        AppState $appState,
        ObjectManagerInterface $objectManager,
        CacheManager $cacheManager,
        ExportProcessorInterface $exportProcessor,
        array $writers = [],
        $name = null
    ) {
        $this->exportProcessor = $exportProcessor;
        $this->writers = $writers;
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
        $this->setDescription('Export settings from "core_config_data" into a file');

        $this->addOption(
            'format',
            'm',
            InputOption::VALUE_OPTIONAL,
            'Format: yaml, json',
            'yaml'
        );

        $this->addOption(
            'hierarchical',
            'a',
            InputOption::VALUE_OPTIONAL,
            'Create a hierarchical or a flat structure (not all export format supports that). Enable with: y',
            'n'
        );

        $this->addOption(
            'filename',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Specifies the export file name. Defaults to "config".'
        );

        $this->addOption(
            'filepath',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Specifies the export path where the export file(s) will be written. Defaults to "var/export/config/Ymd_His/".'
        );

        $this->addOption(
            'include',
            'i',
            InputOption::VALUE_OPTIONAL,
            'Path prefix, multiple values can be comma separated; exports only those paths'
        );

        $this->addOption(
            'includeScope',
            null,
            InputOption::VALUE_OPTIONAL,
            "Scope name, multiple values can be comma separated; exports only those scopes.\n".
            "\t\tTo export only specific scopes add there ID(s) using a colon and separate them with semicolon.\n".
            "\t\tMake sure to use quotes when specifying multiple scope IDs:\n".
            "\t\te.g. --includeScope=\"websites:2;3,stores:2;3;4;5\" will export the settings for website IDs 2 and 3 and for the\n".
            "\t\tstore view IDs 2 to 5"
        );

        $this->addOption(
            'exclude',
            'x',
            InputOption::VALUE_OPTIONAL,
            'Path prefix, multiple values can be comma separated; exports everything except ...'
        );

        $this->addOption(
            'filePerNameSpace',
            's',
            InputOption::VALUE_OPTIONAL,
            'Export each namespace into its own file. Enable with: y',
            'n'
        );

        parent::configure();
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->writeSection('Start Export');

        $format = $this->getFormat();
        if (!array_key_exists($format, $this->writers)) {
            throw new \InvalidArgumentException('Format "' . $format . '" is currently not supported."');
        }

        /** @var \Semaio\ConfigImportExport\Model\File\Writer\WriterInterface $writer */
        $writer = $this->getObjectManager()->create($this->writers[$format]);
        if (!is_object($writer)) {
            throw new \InvalidArgumentException(ucfirst($format) . ' file writer could not be instantiated."');
        }

        $writer->setBaseFilename((string) $input->getOption('filename'));
        $writer->setBaseFilepath((string) $input->getOption('filepath'));

        $writer->setOutput($output);
        $writer->setIsHierarchical('y' === $input->getOption('hierarchical'));
        $writer->setIsFilePerNameSpace('y' === $input->getOption('filePerNameSpace'));

        $include = $input->getOption('include');
        if (!empty($include) && is_string($include) === true) {
            $this->exportProcessor->setInclude($include);
        }

        $includeScope = $input->getOption('includeScope');
        if (!empty($includeScope) && is_string($includeScope) === true) {
            $this->exportProcessor->setIncludeScope($includeScope);
        }

        $exclude = $input->getOption('exclude');
        if (!empty($exclude) && is_string($exclude) === true) {
            $this->exportProcessor->setExclude($exclude);
        }

        $this->exportProcessor->setWriter($writer);
        $this->exportProcessor->setOutput($output);
        $this->exportProcessor->process();

        return 0;
    }
}
