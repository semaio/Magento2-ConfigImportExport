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
     * Process the configuration import/export.
     *
     * @return void
     */
    public function process();

    /**
     * @param OutputInterface $output
     *
     * @return void
     */
    public function setOutput(OutputInterface $output);

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @param string $format
     *
     * @return void
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getFormat();
}
