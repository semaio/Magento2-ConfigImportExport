<?php

namespace Semaio\ConfigImportExport\Model\Processor;

/**
 * Class ExportProcessor
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
class ExportProcessor extends AbstractProcessor implements ExportProcessorInterface
{
    /**
     * @var bool
     */
    private $isHierarchical = false;

    /**
     * Process the import
     */
    public function process()
    {
        $this->writeSection('Start Export');
        $this->getOutput()->writeln('<info>Finished.</info>');
    }

    /**
     * @param bool $isHierarchical
     * @return $this
     */
    public function setIsHierarchical($isHierarchical)
    {
        $this->isHierarchical = $isHierarchical;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsHierarchical()
    {
        return $this->isHierarchical;
    }
}
