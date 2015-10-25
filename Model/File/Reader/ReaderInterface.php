<?php

namespace Semaio\ConfigImportExport\Model\File\Reader;

/**
 * Interface ReaderInterface
 *
 * @package Semaio\ConfigImportExport\Model\File\Reader
 */
interface ReaderInterface
{
    /**
     * @param string $fileName
     * @return array
     */
    public function parse($fileName);
}
