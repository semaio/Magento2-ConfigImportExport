<?php

/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Resolver;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ResolverInterface
{
    /**
     * Resolve the config value.
     *
     * @param string|null $value
     * @param string|null $configPath
     *
     * @return string|null
     */
    public function resolve($value, $configPath = null);

    /**
     * @param string|null $value
     */
    public function supports($value): bool;

    public function setInput(InputInterface $input): void;

    public function getInput(): InputInterface;

    public function setOutput(OutputInterface $output): void;

    public function getOutput(): OutputInterface;

    public function setQuestionHelper(QuestionHelper $questionHelper): void;

    public function getQuestionHelper(): QuestionHelper;
}
