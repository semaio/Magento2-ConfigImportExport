<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Symfony\Component\Console\Output\OutputInterface;

interface AbstractProcessorInterface
{
    /**
     * Process the import
     */
    public function process();

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @param string $format
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getFormat();
}
