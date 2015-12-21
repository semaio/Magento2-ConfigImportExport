<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Test\Unit\Model\File;

use Semaio\ConfigImportExport\Model\File\Finder;

/**
 * Class FinderTest
 *
 * @package Semaio\ConfigImportExport\Test\Unit\Model\File
 */
class FinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * Set up test class
     */
    public function setUp()
    {
        parent::setUp();
        $this->finder = new Finder();
    }

    /**
     * @test
     */
    public function find()
    {
        $this->finder->setBaseFolder('base');
        $this->finder->setFormat('yaml');
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;
        $this->finder->setFolder($folder);
        $this->finder->setEnvironment('dev');

        $result = $this->finder->find();
        $this->assertCount(2, $result);
        $this->assertContains('base.yaml', $result[0]);
        $this->assertContains('env.yaml', $result[1]);
    }

    /**
     * @test
     */
    public function findWithoutBaseFiles()
    {
        $this->finder->setBaseFolder('base2');
        $this->finder->setFormat('yaml');
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;
        $this->finder->setFolder($folder);
        $this->finder->setEnvironment('dev');

        $this->setExpectedException('InvalidArgumentException');
        $this->finder->find();
    }

    /**
     * @test
     */
    public function findWithoutEnvFiles()
    {
        $this->finder->setBaseFolder('base');
        $this->finder->setFormat('yaml');
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;
        $this->finder->setFolder($folder);
        $this->finder->setEnvironment('dev2');

        $this->setExpectedException('InvalidArgumentException');
        $this->finder->find();
    }

    /**
     * @test
     */
    public function setEnvironment()
    {
        $this->setExpectedException('InvalidArgumentException');
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;
        $this->finder->setFolder($folder);
        $this->finder->setEnvironment('abc');
    }

    /**
     * @test
     */
    public function setFolder()
    {
        $this->setExpectedException('InvalidArgumentException');
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test';
        $this->finder->setFolder($folder);
    }
}
