<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Processor;

use Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;

/**
 * Interface ImportProcessorInterface
 *
 * @package Semaio\ConfigImportExport\Model\Processor
 */
interface ImportProcessorInterface extends AbstractProcessorInterface
{
    /**
     * @param ReaderInterface $reader
     */
    public function setReader(ReaderInterface $reader);

    /**
     * @param FinderInterface $finder
     */
    public function setFinder(FinderInterface $finder);
}
