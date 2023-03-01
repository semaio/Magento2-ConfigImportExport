<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Validator;

use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Exception\UnresolveableValueException;
use Semaio\ConfigImportExport\Model\Resolver\EnvironmentVariableResolver;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnvironmentVariableResolverTest extends TestCase
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
     * Set up test class
     */
    protected function setUp(): void
    {
        parent::setUp();

        putenv('HOSTNAME=testvalue1');
        putenv('SUBDOMAIN=testvalue2');
        putenv('CONCAT_THIS=testvalue3');
        putenv('WITH_THIS=testvalue4');
        putenv('FEATURE_12345=testvalue5');

        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->questionHelper = $this->createMock(QuestionHelper::class);
    }

    /**
     * @test
     *
     * @dataProvider resolveDataProvider
     */
    public function validate($value, $expectedResult): void
    {
        $this->assertEquals(
            $this->getEnvironmentVariableResolver()->resolve($value),
            $expectedResult
        );
    }

    public function resolveDataProvider(): \Generator
    {
        yield [
            'test_without_env_var',
            'test_without_env_var',
        ];
        yield [
            '%env(PA)%',
            '%env(PA)%',
        ];
        yield [
            '%env(lowercase)%',
            '%env(lowercase)%',
        ];
        yield [
            '%env(SCRIPT_SOMETHING)%',
            '%env(SCRIPT_SOMETHING)%',
        ];
        yield [
            '%env(PHP_SOMETHING)%',
            '%env(PHP_SOMETHING)%',
        ];
        yield [
            '%env(HTTP_SOMETHING)%',
            '%env(HTTP_SOMETHING)%',
        ];
        yield [
            '%env(SERVER_SOMETHING)%',
            '%env(SERVER_SOMETHING)%',
        ];
        yield [
            '%env(QUERY_SOMETHING)%',
            '%env(QUERY_SOMETHING)%',
        ];
        yield [
            '%env(DOCUMENT_SOMETHING)%',
            '%env(DOCUMENT_SOMETHING)%',
        ];

        yield [
            '%env(HOSTNAME)%',
            'testvalue1',
        ];
        yield [
            'https://%env(SUBDOMAIN)%.example.com',
            'https://testvalue2.example.com',
        ];
        yield [
            '%env(CONCAT_THIS)%%env(WITH_THIS)%',
            'testvalue3testvalue4',
        ];
        yield [
            '%env(FEATURE_12345)%',
            'testvalue5',
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

    public function testItWillPromptForManualValueEntry(): void
    {
        $this->input->expects($this->once())->method('getOption')->with('prompt-missing-env-vars')->willReturn(true);
        $this->input->expects($this->once())->method('isInteractive')->willReturn(true);
        $this->questionHelper->expects($this->once())->method('ask')->willReturn('foo_bar_baz');

        $this->assertEquals('foo_bar_baz', $this->getEnvironmentVariableResolver()->resolve('%env(PROMPT_ENV_VAR)%'));
    }

    public function testItWillRaiseErrorIfEnvVarWasNotFound(): void
    {
        $this->expectException(UnresolveableValueException::class);

        $this->input->expects($this->once())->method('getOption')->with('prompt-missing-env-vars')->willReturn(false);
        $this->input->expects($this->never())->method('isInteractive')->willReturn(false);

        $this->getEnvironmentVariableResolver()->resolve('%env(DOESNOTEXIST)%');
    }

    /**
     * @return EnvironmentVariableResolver
     */
    private function getEnvironmentVariableResolver()
    {
        $resolver = new EnvironmentVariableResolver();
        $resolver->setInput($this->input);
        $resolver->setOutput($this->output);
        $resolver->setQuestionHelper($this->questionHelper);

        return $resolver;
    }
}
