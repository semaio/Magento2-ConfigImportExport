<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Converter;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Model\Converter\ScopeConverter;

class ScopeConverterTest extends TestCase
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @var ScopeConverter
     */
    private $converter;

    /**
     * Set up test class
     */
    protected function setUp(): void
    {
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $this->converter = new ScopeConverter($this->storeManagerMock);
    }

    /**
     * @test
     *
     * @dataProvider itShouldNotConvertNumericScopeIdsProvider
     */
    public function itShouldNotConvertNumericScopeIds($scopeId, $scope): void
    {
        $this->storeManagerMock
            ->expects($this->never())
            ->method('getStore');

        $this->storeManagerMock
            ->expects($this->never())
            ->method('getWebsite');

        $this->assertEquals($scopeId, $this->converter->convert($scopeId, $scope));
    }

    /**
     * @test
     */
    public function itShouldNotConvertStoreCodeValues(): void
    {
        $storeCode = 'mystore';
        $scope = 'stores';
        $storeId = 2;

        $storeStub = $this->getMockBuilder(StoreInterface::class)->getMock();
        $storeStub
            ->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStores')
            ->with($this->equalTo(true), $this->equalTo(true))
            ->willReturn(
                [
                    $storeCode => $storeStub,
                ]
            );

        $this->assertEquals($storeId, $this->converter->convert($storeCode, $scope));
    }

    /**
     * @test
     */
    public function itShouldNotConvertWebsiteCodeValues(): void
    {
        $websiteCode = 'mywebsite';
        $scope = 'websites';
        $websiteId = 3;

        $storeStub = $this->getMockBuilder(WebsiteInterface::class)->getMock();
        $storeStub
            ->expects($this->any())
            ->method('getId')
            ->willReturn($websiteId);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getWebsites')
            ->with($this->equalTo(true), $this->equalTo(true))
            ->willReturn(
                [
                    $websiteCode => $storeStub,
                ]
            );

        $this->assertEquals($websiteId, $this->converter->convert($websiteCode, $scope));
    }

    public function itShouldNotConvertNumericScopeIdsProvider(): array
    {
        return [
            [1, 'default'],
            [2, 'stores'],
            [3, 'websites'],
        ];
    }
}
