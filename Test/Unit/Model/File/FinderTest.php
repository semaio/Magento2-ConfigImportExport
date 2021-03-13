<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\File;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Model\File\Finder;

class FinderTest extends TestCase
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * Set up test class
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = new Finder();
    }

    /**
     * @test
     */
    public function find(): void
    {
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;

        $finder = new Finder();
        $finder->setFolder($folder);
        $finder->setBaseFolder('base');
        $finder->setEnvironment('dev');
        $finder->setFormat('yaml');
        $finder->setDepth('0');

        $result = $finder->find();

        $this->assertCount(2, $result);
        $this->assertStringContainsString('base.yaml', $result[0]);
        $this->assertStringContainsString('env.yaml', $result[1]);
    }

    /**
     * @test
     */
    public function findWithoutBaseFiles(): void
    {
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;

        $finder = new Finder();
        $finder->setFolder($folder);
        $finder->setBaseFolder('base2');
        $finder->setEnvironment('dev');
        $finder->setFormat('yaml');
        $finder->setDepth('0');

        $result = $finder->find();

        $this->assertCount(1, $result);
        $this->assertStringContainsString('env.yaml', $result[0]);
    }

    /**
     * @test
     */
    public function findWithoutEnvFiles(): void
    {
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;

        $finder = new Finder();
        $finder->setFolder($folder);
        $finder->setBaseFolder('base');
        $finder->setEnvironment('dev2');
        $finder->setFormat('yaml');
        $finder->setDepth('0');

        $result = $finder->find();

        $this->assertCount(1, $result);
        $this->assertStringContainsString('base.yaml', $result[0]);
    }

    /**
     * @test
     */
    public function findFilesRecursively(): void
    {
        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;

        $finder = new Finder();
        $finder->setFolder($folder);
        $finder->setBaseFolder('base');
        $finder->setEnvironment('dev');
        $finder->setFormat('yaml');
        $finder->setDepth('>= 0');

        $result = $finder->find();

        $this->assertCount(3, $result);
    }

    /**
     * @test
     */
    public function setEnvironment(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;
        $this->finder->setFolder($folder);
        $this->finder->setEnvironment('abc');
    }

    /**
     * @test
     */
    public function setFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $folder = __DIR__ . DIRECTORY_SEPARATOR . 'Finder' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test';
        $this->finder->setFolder($folder);
    }
}
