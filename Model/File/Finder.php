<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File;

use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder implements FinderInterface
{
    /**
     * @var string
     */
    private $folder;

    /**
     * @var string
     */
    private $baseFolder;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array
     */
    private $environment;

    /**
     * @var string
     */
    private $depth;

    /**
     * @return array
     */
    public function find()
    {
        $baseFiles = $this->search($this->folder . DIRECTORY_SEPARATOR . $this->baseFolder . DIRECTORY_SEPARATOR, $this->depth);
        if (0 === count($baseFiles)) {
            $baseFiles = [];
        }

        $fullEnvPath = '';
        $envFiles = [];
        foreach ($this->environment as $envPath) {
            $fullEnvPath .= $envPath . DIRECTORY_SEPARATOR;
            $find = $this->search($this->folder . DIRECTORY_SEPARATOR . $fullEnvPath, $this->depth);
            $envFiles = array_merge($envFiles, $find);
        }

        if (0 === count($envFiles)) {
            $envFiles = [];
        }

        return array_merge($baseFiles, $envFiles);
    }

    /**
     * @param string $environment
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setEnvironment($environment)
    {
        $environmentFolder = $this->folder . DIRECTORY_SEPARATOR . $environment;
        if (false === is_dir($environmentFolder) || false === is_readable($environmentFolder)) {
            throw new \InvalidArgumentException('Cannot access folders for environment: ' . $environment);
        }
        $this->environment = explode(DIRECTORY_SEPARATOR, trim($environment, DIRECTORY_SEPARATOR));
    }

    /**
     * @param string $folder
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setFolder($folder)
    {
        if (false === is_dir($folder) || false === is_readable($folder)) {
            throw new \InvalidArgumentException('Cannot access folder: ' . $folder);
        }
        $this->folder = rtrim($folder, '/');
    }

    /**
     * @param string $baseFolder
     *
     * @return void
     */
    public function setBaseFolder($baseFolder)
    {
        $this->baseFolder = $baseFolder;
    }

    /**
     * @param string $format
     *
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @param string                         $path
     * @param string|int|string[]|int[]|null $depth
     *
     * @return array
     */
    private function search($path, $depth = null)
    {
        // Remove trailing slash from path
        $path = rtrim($path, '/');

        $finder = new SymfonyFinder();
        $finder->files()
            ->ignoreUnreadableDirs()
            ->name('*.' . $this->format)
            ->followLinks()
            ->in($path);

        if (null !== $depth) {
            $finder->depth($depth);
        }

        $files = [];
        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $files[] = $file->getPathname();
        }

        return $files;
    }

    /**
     * @param string $depth
     */
    public function setDepth($depth): void
    {
        $this->depth = $depth;
    }
}
