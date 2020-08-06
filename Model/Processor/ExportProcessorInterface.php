<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Semaio\ConfigImportExport\Model\File\Writer\WriterInterface;

/**
 * Interface ExportProcessorInterface
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
interface ExportProcessorInterface extends AbstractProcessorInterface
{
    /**
     * @param WriterInterface $writer
     */
    public function setWriter(WriterInterface $writer);

    /**
     * @param string $include
     */
    public function setInclude($include);

    /**
     * @param string $includeScope
     */
    public function setIncludeScope($includeScope);

    /**
     * @param string $exclude
     */
    public function setExclude($exclude);
}
