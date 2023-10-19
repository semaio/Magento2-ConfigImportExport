<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Resolver;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractResolver implements ResolverInterface
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function setQuestionHelper(QuestionHelper $questionHelper): void
    {
        $this->questionHelper = $questionHelper;
    }

    public function getQuestionHelper(): QuestionHelper
    {
        return $this->questionHelper;
    }
}
