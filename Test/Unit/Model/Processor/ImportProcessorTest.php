<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Test\Unit\Model\Processor;

use Semaio\ConfigImportExport\Model\Processor\ImportProcessor;

/**
 * Class ImportProcessorTest
 *
 * @package Semaio\ConfigImportExport\Test\Unit\Model\Processor
 */
class ImportProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $outputMock;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriterMock;

    /**
     * @var \Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface
     */
    private $scopeValidatorMock;

    /**
     * Set up test class
     */
    public function setUp()
    {
        parent::setUp();
        $this->outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->configWriterMock = $this->getMock('Magento\Framework\App\Config\Storage\WriterInterface');
        $this->scopeValidatorMock = $this->getMock('Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface');
    }

    /**
     * @test
     */
    public function processWithoutFiles()
    {
        $finderMock = $this->getMock('Semaio\ConfigImportExport\Model\File\Finder', ['find']);
        $finderMock->expects($this->once())->method('find')->willReturn([]);

        $this->setExpectedException('InvalidArgumentException');

        $processor = new ImportProcessor($this->configWriterMock, $this->scopeValidatorMock);
        $processor->setFinder($finderMock);
        $processor->process();
    }

    /**
     * @test
     */
    public function processWithInvalidScopeData()
    {
        $finderMock = $this->getMock('Semaio\ConfigImportExport\Model\File\Finder', ['find']);
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $parseResult = [
            'test/config/custom_field_one' => [
                'default' => [
                    1 => 'ABC'
                ]
            ]
        ];

        $readerMock = $this->getMock('Semaio\ConfigImportExport\Model\File\Reader\YamlReader', ['parse']);
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->configWriterMock->expects($this->never())->method('save');

        $processor = new ImportProcessor($this->configWriterMock, $this->scopeValidatorMock);
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
        $finderMock = $this->getMock('Semaio\ConfigImportExport\Model\File\Finder', ['find']);
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $parseResult = [
            'test/config/custom_field_one' => [
                'default' => [
                    0 => 'ABC'
                ]
            ]
        ];

        $readerMock = $this->getMock('Semaio\ConfigImportExport\Model\File\Reader\YamlReader', ['parse']);
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->configWriterMock->expects($this->once())->method('save');

        $processor = new ImportProcessor($this->configWriterMock, $this->scopeValidatorMock);
        $processor->setOutput($this->outputMock);
        $processor->setFinder($finderMock);
        $processor->setReader($readerMock);
        $processor->process();
    }
}
