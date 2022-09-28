<?php

/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Resolver;

class EnvironmentVariableResolver
{
    /**
     * @var string
     */
    private $value;

    /**
     * Resolve the config value if it's an environment
     * variable reference.
     *
     * @throws \UnexpectedValueException
     *
     * @param string $value
     * @return string|false the original string, or the resolved
     * string if it's a reference or false on error
     */
    public function resolveValue($value)
    {
        $this->value = $value;
        return preg_replace_callback(
            '/\{env:([^:\}\{]+?)\}/',
            function ($matches) {
                $resolvedValue = getenv($matches[1]);
                if ($resolvedValue === false) {
                    throw new \UnexpectedValueException(sprintf('Environment variable %s does not exist', $this->value));
                }
                return $resolvedValue;
            },
            $value
        );
    }
}
