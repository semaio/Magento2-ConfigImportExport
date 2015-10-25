<?php
namespace Semaio\ConfigImportExport\Command;

use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Semaio\ConfigImportExport\Model\Processor\ExportProcessorInterface;
use Semaio\ConfigImportExport\Model\Processor\ImportProcessorInterface;

/**
 * Class AbstractCommand
 *
 * @package Semaio\ConfigImportExport\Command
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ExportProcessorInterface
     */
    protected $_exportProcessor;

    /**
     * @var ImportProcessorInterface
     */
    protected $_importProcessor;
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param Registry                 $registry
     * @param AppState                 $appState
     * @param ConfigLoader             $configLoader
     * @param ObjectManagerInterface   $objectManager
     * @param CacheManager             $cacheManager
     * @param ExportProcessorInterface $exportProcessor
     * @param ImportProcessorInterface $importProcessor
     * @param null                     $name
     */
    public function __construct(
        Registry $registry,
        AppState $appState,
        ConfigLoader $configLoader,
        ObjectManagerInterface $objectManager,
        CacheManager $cacheManager,
        ExportProcessorInterface $exportProcessor,
        ImportProcessorInterface $importProcessor,
        $name = null
    ) {
        $this->registry = $registry;
        $this->appState = $appState;
        $this->configLoader = $configLoader;
        $this->objectManager = $objectManager;
        $this->cacheManager = $cacheManager;
        $this->_exportProcessor = $exportProcessor;
        $this->_importProcessor = $importProcessor;
        parent::__construct($name);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->appState->setAreaCode('adminhtml');
        $this->objectManager->configure($this->configLoader->load('adminhtml'));
        $this->registry->register('isSecureArea', true);
    }

    /**
     * Retrieve the cache manager
     *
     * @return CacheManager
     */
    public function getCacheManager()
    {
        return $this->cacheManager;
    }
}

