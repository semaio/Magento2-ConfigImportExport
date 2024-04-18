<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Validator;

use Generator;
use Magento\Framework\Encryption\EncryptorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Exception\UnresolveableValueException;
use Semaio\ConfigImportExport\Model\Resolver\EncryptResolver;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptResolverTest extends TestCase
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var MockObject|EncryptorInterface
     */
    private $encryptor;

    /**
     * Set up test class
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->questionHelper = $this->createMock(QuestionHelper::class);
        $this->encryptor = $this->createMock(EncryptorInterface::class);
    }

    /**
     * @test
     *
     * @dataProvider resolveDataProvider
     */
    public function validate($value, $expectedResult): void
    {
        $this->encryptor->expects($this->any())
            ->method('encrypt')
            ->with($expectedResult)
            ->willReturn($expectedResult);

        $this->assertEquals($this->getEncryptResolver()->resolve($value), $expectedResult);
    }

    public function resolveDataProvider(): Generator
    {
        yield [
            'test_without_data_to_encrypt',
            'test_without_data_to_encrypt',
        ];
        yield [
            '%encrypt(data_to_encrypt)%',
            'data_to_encrypt',
        ];
        yield [
            null,
            '',
        ];
        yield [
            false,
            '',
        ];
        yield [
            true,
            '1',
        ];
    }

    public function testItWillRaiseErrorIfEncryptValueIsEmpty(): void
    {
        $this->expectException(UnresolveableValueException::class);

        $this->getEncryptResolver()->resolve('%encrypt()%');
    }

    /**
     * @return EncryptResolver
     */
    private function getEncryptResolver()
    {
        $resolver = new EncryptResolver($this->encryptor);
        $resolver->setInput($this->input);
        $resolver->setOutput($this->output);
        $resolver->setQuestionHelper($this->questionHelper);

        return $resolver;
    }
}
