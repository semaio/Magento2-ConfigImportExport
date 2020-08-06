<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractProcessor
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
abstract class AbstractProcessor implements AbstractProcessorInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $format;

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
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}
