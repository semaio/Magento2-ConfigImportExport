<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Command;

use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

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
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param Registry                 $registry
     * @param AppState                 $appState
     * @param ConfigLoader             $configLoader
     * @param ObjectManagerInterface   $objectManager
     * @param CacheManager             $cacheManager
     * @param null                     $name
     */
    public function __construct(
        Registry $registry,
        AppState $appState,
        ConfigLoader $configLoader,
        ObjectManagerInterface $objectManager,
        CacheManager $cacheManager,
        $name = null
    ) {
        $this->registry = $registry;
        $this->appState = $appState;
        $this->configLoader = $configLoader;
        $this->objectManager = $objectManager;
        $this->cacheManager = $cacheManager;
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

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->input->getOption('format');
    }

    /**
     * @param string $text
     * @param string $style
     */
    public function writeSection($text, $style = 'bg=blue;fg=white')
    {
        $formatter = new FormatterHelper();
        $this->output->writeln(['', $formatter->formatBlock($text, $style, true), '']);
    }

    /**
     * @return ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
