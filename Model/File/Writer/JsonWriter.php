<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Writer;

use Magento\Framework\App\Filesystem\DirectoryList;

class JsonWriter extends AbstractWriter
{
    /**
     * @param string $filename
     * @param array  $data
     *
     * @return void
     */
    protected function _write($filename, array $data)
    {
        // Prepare data
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
        if ($content === false) {
            throw new \RuntimeException('Could not convert data to JSON.');
        }

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
