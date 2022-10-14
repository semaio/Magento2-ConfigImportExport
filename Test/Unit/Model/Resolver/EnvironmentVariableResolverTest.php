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

        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(PA)%'), '%env(PA)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(lowercase)%'), '%env(lowercase)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(SCRIPT_SOMETHING)%'), '%env(SCRIPT_SOMETHING)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(PHP_SOMETHING)%'), '%env(PHP_SOMETHING)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(HTTP_SOMETHING)%'), '%env(HTTP_SOMETHING)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(SERVER_SOMETHING)%'), '%env(SERVER_SOMETHING)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(QUERY_SOMETHING)%'), '%env(QUERY_SOMETHING)%');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(DOCUMENT_SOMETHING)%'), '%env(DOCUMENT_SOMETHING)%');

        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(HOSTNAME)%'), 'testvalue1');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('https://%env(SUBDOMAIN)%.example.com'), 'https://testvalue2.example.com');
        $this->assertEquals($this->environmentVariableResolver->resolveValue('%env(CONCAT_THIS)%%env(WITH_THIS)%'), 'testvalue3testvalue4');

        $this->assertNull($this->environmentVariableResolver->resolveValue(null));
        $this->assertEquals($this->environmentVariableResolver->resolveValue(''), '');
        $this->assertEquals($this->environmentVariableResolver->resolveValue(false), '');
        $this->assertEquals($this->environmentVariableResolver->resolveValue(true), '1');

        $this->expectException(\UnexpectedValueException::class);
        $this->environmentVariableResolver->resolveValue('%env(DOESNOTEXIST)%');
    }

}
