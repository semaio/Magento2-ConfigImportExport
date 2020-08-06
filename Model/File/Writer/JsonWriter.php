<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Writer;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class JsonWriter
 *
 * @package Semaio\ConfigImportExport\Model\File\Writer
 */
class JsonWriter extends AbstractWriter
{
    /**
     * @param string $filename
     * @param array  $data
     */
    protected function _write($filename, array $data)
    {
        // Prepare data
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);

        // Write data to file
        $tmpDirectory = $this->getFilesystem()->getDirectoryWrite(DirectoryList::VAR_DIR);
        $tmpDirectory->writeFile($filename, $content);
        $this->getOutput()->writeln(sprintf(
            '<info>Wrote: %s settings to file %s</info>',
            count($data),
            $tmpDirectory->getAbsolutePath($filename)
        ));
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return 'json';
    }
}
