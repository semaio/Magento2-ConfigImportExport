<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Processor;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Semaio\ConfigImportExport\Model\Converter\ScopeConverterInterface;
use Semaio\ConfigImportExport\Model\Processor\ImportProcessor;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportProcessorTest
 *
 * @package Semaio\ConfigImportExport\Test\Unit\Model\Processor
 */
class ImportProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OutputInterface
     */
    private $outputMock;

    /**
     * @var WriterInterface
     */
    private $configWriterMock;

    /**
     * @var ScopeValidatorInterface
     */
    private $scopeValidatorMock;

    /**
     * @var ScopeConverterInterface
     */
    private $scopeConverterMock;

    /**
     * Set up test class
     */
    public function setUp()
    {
        parent::setUp();
        $this->outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $this->configWriterMock = $this->getMockBuilder(WriterInterface::class)->getMock();
        $this->scopeValidatorMock = $this->getMockBuilder(ScopeValidatorInterface::class)->getMock();
        $this->scopeConverterMock = $this->getMockBuilder(ScopeConverterInterface::class)->getMock();
    }

    /**
     * @test
     */
    public function processWithoutFiles()
    {
        $finderMock = $this->getMockBuilder('Semaio\ConfigImportExport\Model\File\Finder')
            ->setMethods(['find'])
            ->getMock();
        $finderMock
            ->expects($this->once())
            ->method('find')
            ->willReturn([]);

        $this->expectException('InvalidArgumentException');

        $processor = new ImportProcessor($this->configWriterMock, $this->scopeValidatorMock, $this->scopeConverterMock);
        $processor->setFinder($finderMock);
        $processor->process();
    }

    /**
     * @test
     */
    public function processWithInvalidScopeData()
    {
        $finderMock = $this->getMockBuilder('Semaio\ConfigImportExport\Model\File\Finder')
            ->setMethods(['find'])
            ->getMock();
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $parseResult = [
            'test/config/custom_field_one' => [
                'default' => [
                    1 => 'ABC'
                ]
            ]
        ];

        $readerMock = $this->getMockBuilder('Semaio\ConfigImportExport\Model\File\Reader\YamlReader')
            ->setMethods(['parse'])
            ->getMock();
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->configWriterMock->expects($this->never())->method('save');

        $processor = new ImportProcessor($this->configWriterMock, $this->scopeValidatorMock, $this->scopeConverterMock);
        $processor->setFormat('yaml');
        $processor->setOutput($this->outputMock);
        $processor->setFinder($finderMock);
        $processor->setReader($readerMock);
        $processor->process();
    }

    /**
     * @test
     */
    public function process()
    {
        $finderMock = $this->getMockBuilder('Semaio\ConfigImportExport\Model\File\Finder')
            ->setMethods(['find'])
            ->getMock();
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $parseResult = [
            'test/config/custom_field_one' => [
                'default' => [
                    0 => 'ABC'
                ]
            ]
        ];

        $readerMock = $this->getMockBuilder('Semaio\ConfigImportExport\Model\File\Reader\YamlReader')
            ->setMethods(['parse'])
            ->getMock();
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->configWriterMock->expects($this->once())->method('save');

        $processor = new ImportProcessor($this->configWriterMock, $this->scopeValidatorMock, $this->scopeConverterMock);
        $processor->setOutput($this->outputMock);
        $processor->setFinder($finderMock);
        $processor->setReader($readerMock);
        $processor->process();
    }
}
