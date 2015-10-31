<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Processor;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * Class AbstractProcessor
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
abstract class AbstractProcessor implements AbstractProcessorInterface
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $text
     * @param string $style
     */
    public function writeSection($text, $style = 'bg=blue;fg=white')
    {
        $formatter = new FormatterHelper();
        $this->getOutput()->writeln(['', $formatter->formatBlock($text, $style, true), '']);
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->getInput()->getOption('format');
    }
}
