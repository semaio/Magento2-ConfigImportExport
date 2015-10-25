<?php

namespace Semaio\ConfigImportExport\Model\Processor;

/**
 * Interface ExportProcessorInterface
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
interface ExportProcessorInterface extends AbstractProcessorInterface
{
    /**
     * @param bool $isHierarchical
     * @return $this
     */
    public function setIsHierarchical($isHierarchical);

    /**
     * @return bool
     */
    public function getIsHierarchical();
}
