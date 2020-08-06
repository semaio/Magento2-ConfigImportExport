<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\File\Reader;

use Semaio\ConfigImportExport\Model\File\Reader\YamlReader;

/**
 * Class YamlReaderTest
 *
 * @package Semaio\ConfigImportExport\Test\Unit\Model\File\Reader
 */
class YamlReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var YamlReader
     */
    private $reader;

    /**
     * Set up test class
     */
    public function setUp()
    {
        parent::setUp();

        $this->reader = new YamlReader();
    }

    /**
     * @test
     * @dataProvider provideFiles
     */
    public function parse($file)
    {
        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'YamlReaderTest' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $result = $this->reader->parse($baseDir . $file);
        $expectedResult = include $baseDir . 'ex-parse.php';
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function provideFiles()
    {
        return [
            ['file' => 'fx-test.yaml'],
            ['file' => 'fx-test-hierarchical.yaml']
        ];
    }
}
