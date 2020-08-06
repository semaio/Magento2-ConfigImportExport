<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Writer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface WriterInterface
 *
 * @package Semaio\ConfigImportExport\Model\File\Writer
 */
interface WriterInterface
{
    /**
     * @param array $data
     */
    public function write(array $data = []);

    /**
     * @return string
     */
    public function getFileExtension();

    /**
     * @param string $baseFilename
     */
    public function setBaseFilename($baseFilename);

    /**
     * @return string
     */
    public function getBaseFilename();

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @param bool $isHierarchical
     * @return $this
     */
    public function setIsHierarchical($isHierarchical);

    /**
     * @return bool
     */
    public function getIsHierarchical();

    /**
     * @param bool $isFilePerNameSpace
     * @return $this
     */
    public function setIsFilePerNameSpace($isFilePerNameSpace);

    /**
     * @return bool
     */
    public function getIsFilePerNameSpace();
}
