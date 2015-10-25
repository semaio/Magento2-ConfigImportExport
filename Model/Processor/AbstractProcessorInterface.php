<?php

namespace Semaio\ConfigImportExport\Model\Processor;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface AbstractProcessorInterface
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
interface AbstractProcessorInterface
{
    /**
     * Process the import
     */
    public function process();

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input);

    /**
     * @return InputInterface
     */
    public function getInput();

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @return string
     */
    public function getFormat();
}
