<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Test\Unit\Model\Validator;

use Semaio\ConfigImportExport\Model\Validator\ScopeValidator;

/**
 * Class ScopeValidatorTest
 *
 * @package Semaio\ConfigImportExport\Test\Unit\Model\Validator
 */
class ScopeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScopeValidator
     */
    private $validator;

    /**
     * Set up test class
     */
    public function setUp()
    {
        parent::setUp();

        $storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $storeManagerMock->expects($this->any())->method('getWebsites')->willReturn([1 => 'ABC']);
        $storeManagerMock->expects($this->any())->method('getStores')->willReturn([2 => 'ABC']);

        $this->validator = new ScopeValidator($storeManagerMock);
    }

    /**
     * @test
     */
    public function validate()
    {
        $this->assertTrue($this->validator->validate('default', 0));
        $this->assertFalse($this->validator->validate('default', 1));

        $this->assertTrue($this->validator->validate('websites', 1));
        $this->assertFalse($this->validator->validate('websites', 2));

        $this->assertTrue($this->validator->validate('stores', 2));
        $this->assertFalse($this->validator->validate('stores', 3));
    }
}
