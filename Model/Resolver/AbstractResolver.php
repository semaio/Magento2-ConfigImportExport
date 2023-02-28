<?php

declare(strict_types=1);

namespace Semaio\ConfigImportExport\Model\Resolver;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractResolver implements ResolverInterface
{
    private InputInterface $input;
    private OutputInterface $output;
    private QuestionHelper $questionHelper;

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
