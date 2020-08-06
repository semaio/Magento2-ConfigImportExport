<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\File\Reader;

use Semaio\ConfigImportExport\Model\File\Reader\JsonReader;

/**
 * Class JsonReaderTest
 *
 * @package Semaio\ConfigImportExport\Test\Unit\Model\File\Reader
 */
class JsonReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var JsonReader
     */
    private $reader;

    /**
     * Set up test class
     */
    public function setUp()
    {
        parent::setUp();

        $this->reader = new JsonReader();
    }

    /**
     * @test
     * @dataProvider provideFiles
     */
    public function parse($file)
    {
        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'JsonReaderTest' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $result = $this->reader->parse($baseDir . $file);
        $expectedResult = include $baseDir . 'ex-parse.php';
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function parseInvalidFile()
    {
        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'JsonReaderTest' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;

        $this->expectException('InvalidArgumentException');

        $this->reader->parse($baseDir . 'fx-test-invalid.json');
    }

    /**
     * @return array
     */
    public function provideFiles()
    {
        return [
            ['file' => 'fx-test.json'],
            ['file' => 'fx-test-hierarchical.json']
        ];
    }
}
