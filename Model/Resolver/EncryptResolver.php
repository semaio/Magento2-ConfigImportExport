<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Resolver;

use Magento\Framework\Encryption\EncryptorInterface;
use Semaio\ConfigImportExport\Exception\UnresolveableValueException;
use function strlen;

class EncryptResolver extends AbstractResolver
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    public function __construct(EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * Resolve the config value if wrapped with '%encrypt(value)%', this method encrypts the value.
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

        $value = (string)$value;
        if ($value === '%encrypt()%') {
            throw new UnresolveableValueException('Please specify a valid value to encrypt.');
        }

        $valueToEncrypt = preg_replace_callback(
            '/\%encrypt\(([^)]+)\)\%/',
            function ($matches) {
                return $matches[1];
            },
            $value
        );

        return $this->encryptor->encrypt($valueToEncrypt);
    }

    /**
     * @inheritDoc
     */
    public function supports($value, $configPath = null): bool
    {
        return 0 === strncmp((string)$value, '%encrypt', strlen('%encrypt'));
    }
}
