<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Reader;

/**
 * Class AbstractReader
 *
 * @package Semaio\ConfigImportExport\Model\File\Reader
 */
abstract class AbstractReader implements ReaderInterface
{
    /**
     * @param array $content
     * @return array
     */
    public function normalize(array $content)
    {
        $return = [];
        foreach ($content as $nameSpace => $settings) {
            if (strpos($nameSpace, '/') === false) {
                $cfgValues = $this->flatten($nameSpace, $settings);
                $return = array_merge($return, $cfgValues);
            } else {
                $return[$nameSpace] = $settings;
            }
        }

        return $return;
    }

    /**
     * @param string $nameSpace1
     * @param array  $settings1
     * @return array
     */
    public function flatten($nameSpace1, array $settings1)
    {
        $return = [];
        foreach ($settings1 as $nameSpace2 => $settings2) {
            foreach ($settings2 as $nameSpace3 => $settings3) {
                $return[$nameSpace1 . '/' . $nameSpace2 . '/' . $nameSpace3] = $settings3;
            }
        }

        return $return;
    }
}
