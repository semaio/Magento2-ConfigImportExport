<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Writer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Symfony\Component\Yaml\Dumper;

/**
 * Class YamlWriter
 *
 * @package Semaio\ConfigImportExport\Model\File\Writer
 */
class YamlWriter extends AbstractWriter
{
    /**
     * @param string $filename
     * @param array  $data
     */
    protected function _write($filename, array $data)
    {
        // Prepare data
        if (true === $this->getIsHierarchical()) {
            $yaml = new Dumper();
            $yaml->setIndentation(2);
            $content = $yaml->dump($data, 5, 0, false, true);
        } else {
            $content = $this->generateYaml($data);
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
        return 'yaml';
    }

    /**
     * Custom format with nice headers only for flat structure available
     *
     * @param array $data
     * @return string
     */
    private function generateYaml(array $data)
    {
        $fileContent = [];
        $header = [];
        foreach ($data as $path => $scopes) {
            $paths = explode('/', $path);
            if (!isset($header[$paths[0]])) {
                $header[$paths[0]] = true;
                $length = strlen($paths[0]) + 2;
                $fileContent[] = PHP_EOL . $this->getIndentation($length, '#') . PHP_EOL . '# ' . $paths[0] . PHP_EOL . $this->getIndentation($length, '#');
            }

            $fileContent[] = $path . ':';
            foreach ($scopes as $scope => $scopeValues) {
                $fileContent[] = $this->getIndentation(2) . $scope . ':';
                foreach ($scopeValues as $scopeId => $value) {
                    $fileContent[] = $this->getIndentation(4) . $scopeId . ': ' . $this->prepareValue($value);
                }
            }
        }

        return implode(PHP_EOL, $fileContent);
    }

    /**
     * @param  int   $length
     * @param string $string
     *
     * @return string
     */
    private function getIndentation($length, $string = ' ')
    {
        return str_repeat($string, $length);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function prepareValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        if (strpos($value, "\n") !== false) {
            $values = explode("\n", $value);
            foreach ($values as &$line) {
                $line = $this->getIndentation(8) . $line;
            }
            $value = implode("\n", $values);

            return "|\n" . $value;
        }

        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
    }
}
