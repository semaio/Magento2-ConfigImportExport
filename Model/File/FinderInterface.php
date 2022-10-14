<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\File;

interface FinderInterface
{
    /**
     * @return array
     */
    public function find();

    /**
     * @param string $environment
     *
     * @return void
     */
    public function setEnvironment($environment);

    /**
     * @param mixed $folder
     *
     * @return void
     */
    public function setFolder($folder);

    /**
     * @param mixed $baseFolder
     *
     * @return void
     */
    public function setBaseFolder($baseFolder);

    /**
     * @param mixed $format
     *
     * @return void
     */
    public function setFormat($format);

    /**
     * @param mixed $depth
     *
     * @return void
     */
    public function setDepth($depth);
}
