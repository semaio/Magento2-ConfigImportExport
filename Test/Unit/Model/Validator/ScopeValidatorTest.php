<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Validator;

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidator;

class ScopeValidatorTest extends TestCase
{
    /**
     * @var ScopeValidator
     */
    private $validator;

    /**
     * @var MockObject|WebsiteInterface
     */
    protected $mockWebsiteOne = null;

    /**
     * Set up test class
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockWebsiteOne = $this->getMockBuilder(WebsiteInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $storeManagerMock->method('getWebsites')->willReturn([1 => $this->mockWebsiteOne]);
        $storeManagerMock->method('getStores')->willReturn([2 => 'ABC']);

        $this->validator = new ScopeValidator($storeManagerMock);
    }

    /**
     * @test
     */
    #[Test]
    public function validate(): void
    {
        $this->assertTrue($this->validator->validate('default', 0));
        $this->assertFalse($this->validator->validate('default', 1));

        $this->assertTrue($this->validator->validate('websites', 1));
        $this->assertFalse($this->validator->validate('websites', 2));

        $this->assertTrue($this->validator->validate('stores', 2));
        $this->assertFalse($this->validator->validate('stores', 3));
    }

    /**
     * @test
     */
    #[Test]
    public function validateNonNumericWebsite(): void
    {
        $existingWebsiteCode = 'my-cool-website';

        $this->mockWebsiteOne->method('getCode')
            ->willReturn($existingWebsiteCode);

        $this->assertTrue($this->validator->validate('websites', $existingWebsiteCode));
        $this->assertFalse($this->validator->validate('websites', 'am i real?'));
    }
}
