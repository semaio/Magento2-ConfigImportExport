<?php

namespace Semaio\ConfigImportExport\Model\Processor;

/**
 * Interface ImportProcessorInterface
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
interface ImportProcessorInterface extends AbstractProcessorInterface
{
    /**
     * @param string $folder
     * @throws \InvalidArgumentException
     */
    public function setFolder($folder);

    /**
     * @param string $environment
     * @throws \InvalidArgumentException
     */
    public function setEnvironment($environment);
}
