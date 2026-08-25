<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Processor;

use InvalidArgumentException;
use Magento\Framework\App\Config\ConfigPathResolver;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Stdlib\ArrayManager;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Model\Converter\ScopeConverterInterface;
use Semaio\ConfigImportExport\Model\File\Finder;
use Semaio\ConfigImportExport\Model\File\Reader\YamlReader;
use Semaio\ConfigImportExport\Model\Processor\ImportProcessor;
use Semaio\ConfigImportExport\Model\Validator\ScopeValidatorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProcessorTest extends TestCase
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
     * @var DeploymentConfigWriter
     */
    private $deploymentConfigWriterMock;

    /**
     * @var ConfigPathResolver
     */
    private $configPathResolverMock;

    /**
     * @var ArrayManager
     */
    private $arrayManagerMock;

    /**
     * @var ResourceConnection
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface
     */
    private $connectionMock;

    /**
     * Set up test class
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $this->configWriterMock = $this->getMockBuilder(WriterInterface::class)->getMock();
        $this->scopeValidatorMock = $this->getMockBuilder(ScopeValidatorInterface::class)->getMock();
        $this->scopeConverterMock = $this->getMockBuilder(ScopeConverterInterface::class)->getMock();
        $this->deploymentConfigWriterMock = $this->getMockBuilder(DeploymentConfigWriter::class)->disableOriginalConstructor()->getMock();
        $this->configPathResolverMock = $this->getMockBuilder(ConfigPathResolver::class)->disableOriginalConstructor()->getMock();
        $this->arrayManagerMock = $this->getMockBuilder(ArrayManager::class)->disableOriginalConstructor()->getMock();

        $selectMock = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $selectMock->method('from')->willReturnSelf();
        $selectMock->method('where')->willReturnSelf();

        $this->connectionMock = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->connectionMock->method('select')->willReturn($selectMock);

        $this->resourceConnectionMock = $this->getMockBuilder(ResourceConnection::class)->disableOriginalConstructor()->getMock();
        $this->resourceConnectionMock->method('getConnection')->willReturn($this->connectionMock);
        $this->resourceConnectionMock->method('getTableName')->willReturn('core_config_data');
    }

    /**
     * @return ImportProcessor
     */
    private function createProcessor(): ImportProcessor
    {
        return new ImportProcessor(
            $this->configWriterMock,
            $this->scopeValidatorMock,
            $this->scopeConverterMock,
            $this->deploymentConfigWriterMock,
            $this->configPathResolverMock,
            $this->arrayManagerMock,
            $this->resourceConnectionMock,
            []
        );
    }

    /**
     * @test
     */
    public function processWithoutFiles(): void
    {
        $finderMock = $this->getMockBuilder(Finder::class)
            ->onlyMethods(['find'])
            ->getMock();
        $finderMock
            ->expects($this->once())
            ->method('find')
            ->willReturn([]);

        $this->expectException(InvalidArgumentException::class);

        $processor = $this->createProcessor();
        $inputMock = $this->getMockBuilder(InputInterface::class)->getMock();
        $inputMock->method('getOption')->with('allow-empty-directories')->willReturn(false);
        $processor->setInput($inputMock);
        $processor->setFinder($finderMock);
        $processor->process();
    }

    /**
     * @test
     */
    public function processWithInvalidScopeData(): void
    {
        $finderMock = $this->getMockBuilder(Finder::class)
            ->onlyMethods(['find'])
            ->getMock();
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $parseResult = [
            'test/config/custom_field_one' => [
                'default' => [
                    1 => 'ABC',
                ],
            ],
        ];

        $readerMock = $this->getMockBuilder(YamlReader::class)
            ->onlyMethods(['parse'])
            ->getMock();
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->configWriterMock->expects($this->never())->method('save');

        $processor = $this->createProcessor();
        $processor->setInput($this->getMockBuilder(InputInterface::class)->getMock());
        $processor->setFormat('yaml');
        $processor->setOutput($this->outputMock);
        $processor->setFinder($finderMock);
        $processor->setReader($readerMock);
        $processor->process();
    }

    /**
     * @test
     */
    public function process(): void
    {
        $finderMock = $this->getMockBuilder(Finder::class)
            ->onlyMethods(['find'])
            ->getMock();
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $parseResult = [
            'test/config/custom_field_one' => [
                'default' => [
                    0 => 'ABC',
                ],
            ],
            'test/config/custom_field_to_be_deleted' => [
                'default' => [
                    0 => '!!DELETE',
                ],
            ],
            'test/config/custom_field_to_be_keeped' => [
                'default' => [
                    0 => 'VALUE_THAT_SHOULD_NOT_BE_PROCESSED',
                ],
            ],
            'test/config/custom_field_to_be_keeped' => [
                'default' => [
                    0 => '!!KEEP',
                ],
            ],
        ];

        $readerMock = $this->getMockBuilder(YamlReader::class)
            ->onlyMethods(['parse'])
            ->getMock();
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeValidatorMock->expects($this->exactly(3))->method('validate')->willReturn(true);
        $this->configWriterMock->expects($this->once())->method('save');
        $this->configWriterMock->expects($this->once())->method('delete');

        $processor = $this->createProcessor();
        $processor->setInput($this->getMockBuilder(InputInterface::class)->getMock());
        $processor->setOutput($this->outputMock);
        $processor->setFinder($finderMock);
        $processor->setReader($readerMock);
        $processor->process();
    }

    /**
     * @test
     */
    public function processWithIfNotSetModifierAndNoExistingValue(): void
    {
        $parseResult = [
            'test/config/custom_field_if_not_set' => [
                'if' => 'not-set',
                'default' => [
                    0 => 'ABC',
                ],
            ],
        ];

        $this->connectionMock->method('fetchOne')->willReturn(false);

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->configWriterMock->expects($this->once())->method('save')->with('test/config/custom_field_if_not_set', 'ABC');

        $this->processParseResult($parseResult);
    }

    /**
     * @test
     */
    public function processWithIfNotSetModifierAndExistingValue(): void
    {
        $parseResult = [
            'test/config/custom_field_if_not_set' => [
                'if' => 'not-set',
                'default' => [
                    0 => 'ABC',
                ],
            ],
        ];

        $this->connectionMock->method('fetchOne')->willReturn('123');

        $this->scopeValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->configWriterMock->expects($this->never())->method('save');

        $this->processParseResult($parseResult);
    }

    /**
     * @test
     */
    public function processWithIfNotSetModifierOnlyGuardsFlaggedPaths(): void
    {
        $parseResult = [
            'test/config/custom_field_if_not_set' => [
                'if' => 'not-set',
                'default' => [
                    0 => 'ABC',
                ],
            ],
            'test/config/custom_field_regular' => [
                'default' => [
                    0 => 'DEF',
                ],
            ],
        ];

        $this->connectionMock->method('fetchOne')->willReturn('123');

        $this->scopeValidatorMock->expects($this->exactly(2))->method('validate')->willReturn(true);
        $this->configWriterMock->expects($this->once())->method('save')->with('test/config/custom_field_regular', 'DEF');

        $this->processParseResult($parseResult);
    }

    /**
     * @test
     */
    public function processWithInvalidIfModifierSkipsPath(): void
    {
        $parseResult = [
            'test/config/custom_field_invalid_if' => [
                'if' => 'always',
                'default' => [
                    0 => 'ABC',
                ],
            ],
        ];

        $this->scopeValidatorMock->expects($this->never())->method('validate');
        $this->configWriterMock->expects($this->never())->method('save');

        $this->processParseResult($parseResult);
    }

    /**
     * Run the import processor against a single parsed configuration file.
     */
    private function processParseResult(array $parseResult): void
    {
        $finderMock = $this->getMockBuilder(Finder::class)
            ->onlyMethods(['find'])
            ->getMock();
        $finderMock->expects($this->once())->method('find')->willReturn(['abc.yaml']);

        $readerMock = $this->getMockBuilder(YamlReader::class)
            ->onlyMethods(['parse'])
            ->getMock();
        $readerMock->expects($this->once())->method('parse')->willReturn($parseResult);

        $this->scopeConverterMock->method('convert')->willReturn(0);

        $processor = $this->createProcessor();
        $processor->setInput($this->getMockBuilder(InputInterface::class)->getMock());
        $processor->setOutput($this->outputMock);
        $processor->setFinder($finderMock);
        $processor->setReader($readerMock);
        $processor->process();
    }
}
