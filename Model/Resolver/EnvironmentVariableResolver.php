<?php

/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Resolver;

use Semaio\ConfigImportExport\Exception\UnresolveableValueException;
use Symfony\Component\Console\Question\Question;

class EnvironmentVariableResolver extends AbstractResolver
{
    /**
     * Resolve the config value if it's an environment variable reference.
     *
     * @param string|null $value
     * @param string|null $configPath
     *
     * @return string|null
     *
     * @throws UnresolveableValueException
     */
    public function resolve($value, $configPath = null)
    {
        if ($value === null) {
            return null;
        }

        try {
            $value = (string) $value;

            $value = preg_replace_callback(
                '/\%env\((?!PHP_|HTTP_|SERVER_|SCRIPT_|QUERY_|DOCUMENT_)([A-Z0-9\_]{3,})\)\%/',
                function ($matches) {
                    $resolvedValue = getenv($matches[1]);
                    if ($resolvedValue === false) {
                        throw new \UnexpectedValueException(sprintf('Environment variable %s does not exist', $matches[1]));
                    }

                    return $resolvedValue;
                },
                $value
            );
        } catch (\UnexpectedValueException $exception) {
            if ($this->getInput()->getOption('prompt-missing-env-vars') && $this->getInput()->isInteractive()) {
                $value = $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), new Question($configPath . ': '));
            } else {
                throw new UnresolveableValueException($exception->getMessage());
            }
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function supports($value): bool
    {
        return 0 === strncmp((string) $value, '%env', \strlen('%env'));
    }
}
