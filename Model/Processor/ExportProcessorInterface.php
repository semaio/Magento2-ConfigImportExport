<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Semaio\ConfigImportExport\Model\File\Writer\WriterInterface;

interface ExportProcessorInterface extends AbstractProcessorInterface
{
    /**
     * @param WriterInterface $writer
     *
     * @return void
     */
    public function setWriter(WriterInterface $writer);

    /**
     * @param string $include
     *
     * @return void
     */
    public function setInclude($include);

    /**
     * @param string $includeScope
     *
     * @return void
     */
    public function setIncludeScope($includeScope);

    /**
     * @param string $includeScopeId
     *
     * @return void
     */
    public function setIncludeScopeId($includeScopeId);

    /**
     * @param string $exclude
     *
     * @return void
     */
    public function setExclude($exclude);
}
