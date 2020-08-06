<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Reader;

/**
 * Class YamlReader
 *
 * @package Semaio\ConfigImportExport\Model\File\Reader
 */
class JsonReader extends AbstractReader
{
    /**
     * @param string $fileName
     * @return array
     */
    public function parse($fileName)
    {
        $content = json_decode(file_get_contents($fileName), true);

        if (0 !== json_last_error()) {
            throw new \InvalidArgumentException('Could not parse JSON file: ' . $fileName . '. ' . json_last_error_msg());
        }

        return $this->normalize($content);
    }
}
