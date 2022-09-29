<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Validator;

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Model\Resolver\EnvironmentVariableResolver;

class EnvironmentVariableResolverTest extends TestCase
{
    /**
     * @var EnvironmentVariableResolver
     */
    private $environmentVariableResolver;

    /**
     * Set up test class
     */
    protected function setUp(): void
    {
        parent::setUp();

        putenv('HOSTNAME=testvalue1');
        putenv('SUBDOMAIN=testvalue2');
        putenv('CONCAT_THIS=testvalue3');
        putenv('WITH_THIS=testvalue4');

        $this->environmentVariableResolver = new EnvironmentVariableResolver();
    }

    /**
     * @test
     */
    public function validate(): void
    {
        $this->assertEquals($this->environmentVariableResolver->resolveValue('test_without_env_var'), 'test_without_env_var');

        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(HOSTNAME)%'), 'testvalue1');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('https://%env(SUBDOMAIN)%.example.com'), 'https://testvalue2.example.com');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(CONCAT_THIS)%%env(WITH_THIS)%'), 'testvalue3testvalue4');

        $this->expectException(\UnexpectedValueException::class);
        $this->environmentVariableResolver->resolveValue('%env(DOESNOTEXIST)%');
    }

}
