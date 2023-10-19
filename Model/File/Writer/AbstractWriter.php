<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File\Writer;

use Magento\Framework\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @var string
     */
    private $baseFilename = null;

    /**
     * @var string
     */
    private $baseFilepath = null;

    /**
     * @var bool
     */
    private $isHierarchical = false;

    /**
     * @var bool
     */
    private $isFilePerNameSpace = false;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * AbstractWriter constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function write(array $data = [])
    {
        if (true === $this->getIsHierarchical()) {
            $preparedData = $this->_prepareDataHierarchical($data);
        } else {
            $preparedData = $this->_prepareDataFlat($data);
        }

        if ($this->getIsFilePerNameSpace()) {
            $namespacedData = [];
            foreach ($preparedData as $key => $item) {
                $tmpPath = explode('/', $key);
                if (!isset($namespacedData[$tmpPath[0]])) {
                    $namespacedData[$tmpPath[0]] = [];
                }
                $namespacedData[$tmpPath[0]][$key] = $item;
            }

            foreach ($namespacedData as $namespace => $configData) {
                $this->_write($this->getFilenameWithPath($namespace), $configData);
            }
        } else {
            $this->_write($this->getFilenameWithPath(), $preparedData);
        }
    }

    /**
     * @param string $filename
     * @param array  $data
     *
     * @return void
     */
    abstract protected function _write($filename, array $data);

    /**
     * @param array $exportData
     *
     * @return array
     */
    protected function _prepareDataHierarchical(array $exportData)
    {
        $return = [];
        foreach ($exportData as $row) {
            list($firstPart, $secondPart, $thirdPart) = explode('/', $row['path'], 3);
            $return[$firstPart][$secondPart][$thirdPart][$row['scope']][$row['scope_id']] = $row['value'];
        }

        return $return;
    }

    /**
     * @param array $exportData
     *
     * @return array
     */
    protected function _prepareDataFlat(array $exportData)
    {
        $return = [];
        foreach ($exportData as $row) {
            if (!isset($return[$row['path']])) {
                $return[$row['path']] = [];
            }
            if (!isset($return[$row['path']][$row['scope']])) {
                $return[$row['path']][$row['scope']] = [];
            }
            $return[$row['path']][$row['scope']]['' . $row['scope_id']] = $row['value'];
        }

        return $return;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param string $baseFilename
     *
     * @return void
     */
    public function setBaseFilename($baseFilename)
    {
        if (strpos($baseFilename, DIRECTORY_SEPARATOR) !== false) {
            throw new \InvalidArgumentException(
                'The filename must not contain a directory separator. Use "--filepath" option to set the output directory.'
            );
        }

        $this->baseFilename = $baseFilename;
    }

    /**
     * @return string
     */
    public function getBaseFilename()
    {
        return $this->baseFilename;
    }

    /**
     * @param string $baseFilepath
     *
     * @return void
     */
    public function setBaseFilepath($baseFilepath)
    {
        if ($baseFilepath === '') {
            $baseFilepath = implode(DIRECTORY_SEPARATOR, ['var', 'export', 'config', date('Ymd_His')]);
        }

        $this->baseFilepath = ltrim(rtrim($baseFilepath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getBaseFilepath()
    {
        return $this->baseFilepath;
    }

    /**
     * @param OutputInterface $output
     *
     * @return void
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param bool $isHierarchical
     *
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

    /**
     * @param bool $isFilePerNameSpace
     *
     * @return $this
     */
    public function setIsFilePerNameSpace($isFilePerNameSpace)
    {
        $this->isFilePerNameSpace = $isFilePerNameSpace;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsFilePerNameSpace()
    {
        return $this->isFilePerNameSpace;
    }

    /**
     * @param null|string $namespace
     *
     * @return string
     */
    private function getFilenameWithPath($namespace = null)
    {
        if ($namespace !== null) {
            // Prefix namespace with filename when provided
            $filename = $this->getBaseFilename() === '' ? $namespace : $this->getBaseFilename() . $namespace;
        } else {
            // Set default filename to 'config' when none provided
            $filename = $this->getBaseFilename() === '' ? 'config' : $this->getBaseFilename();
        }

        return $this->getBaseFilepath() . $filename . '.' . $this->getFileExtension();
    }
}
