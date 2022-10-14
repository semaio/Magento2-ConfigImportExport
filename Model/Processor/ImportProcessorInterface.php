<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Semaio\ConfigImportExport\Model\File\FinderInterface;
use Semaio\ConfigImportExport\Model\File\Reader\ReaderInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;

interface ImportProcessorInterface extends AbstractProcessorInterface
{
    /**
     * @param InputInterface $input
     *
     * @return void
     */
    public function setInput(InputInterface $input);

    /**
     * @return InputInterface
     */
    public function getInput();

    /**
     * @param QuestionHelper $questionHelper
     *
     * @return void
     */
    public function setQuestionHelper(QuestionHelper $questionHelper);

    /**
     * @return QuestionHelper
     */
    public function getQuestionHelper();

    /**
     * @param ReaderInterface $reader
     *
     * @return void
     */
    public function setReader(ReaderInterface $reader);

    /**
     * @param FinderInterface $finder
     *
     * @return void
     */
    public function setFinder(FinderInterface $finder);
}
