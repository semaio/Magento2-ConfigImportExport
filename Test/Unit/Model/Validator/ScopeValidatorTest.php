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
            ->onlyMethods(['getCode'])
            ->getMockForAbstractClass();

        $storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $storeManagerMock->expects($this->any())->method('getWebsites')->willReturn([1 => $this->mockWebsiteOne]);
        $storeManagerMock->expects($this->any())->method('getStores')->willReturn([2 => 'ABC']);

        $this->validator = new ScopeValidator($storeManagerMock);
    }

    /**
     * @test
     */
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
    public function validateNonNumericWebsite(): void
    {
        $existingWebsiteCode = 'my-cool-website';

        $this->mockWebsiteOne->expects($this->any())->method('getCode')
            ->will($this->returnValue($existingWebsiteCode));

        $this->assertTrue($this->validator->validate('websites', $existingWebsiteCode));
        $this->assertFalse($this->validator->validate('websites', 'am i real?'));
    }
}
