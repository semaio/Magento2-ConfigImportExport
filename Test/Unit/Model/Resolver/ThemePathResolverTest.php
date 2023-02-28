<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Test\Unit\Model\Validator;

use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Design\ThemeInterface;
use PHPUnit\Framework\TestCase;
use Semaio\ConfigImportExport\Exception\UnresolveableValueException;
use Semaio\ConfigImportExport\Model\Resolver\ThemePathResolver;

class ThemePathResolverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccessfulReplacement(): void
    {
        $theme = $this->createMock(ThemeInterface::class);
        $theme->expects($this->any())->method('getId')->willReturn(13);

        $themeProvider = $this->createMock(ThemeProviderInterface::class);
        $themeProvider->expects($this->once())
            ->method('getThemeByFullPath')
            ->with('frontend/Vendor/theme')
            ->willReturn($theme);

        $themePathResolver = new ThemePathResolver($themeProvider);
        $this->assertEquals('13', $themePathResolver->resolve('%theme(frontend/Vendor/theme)%'));
    }

    public function testItWillRaiseErrorIfJustWrapperIsGiven(): void
    {
        $this->expectException(UnresolveableValueException::class);

        $themeProvider = $this->createMock(ThemeProviderInterface::class);

        $themePathResolver = new ThemePathResolver($themeProvider);
        $this->assertEquals('13', $themePathResolver->resolve('%theme()%'));
    }

    public function testItWillRaiseErrorIfThemeWasNotFound(): void
    {
        $this->expectException(UnresolveableValueException::class);

        $theme = $this->createMock(ThemeInterface::class);
        $theme->expects($this->any())->method('getId')->willReturn(null);

        $themeProvider = $this->createMock(ThemeProviderInterface::class);
        $themeProvider->expects($this->once())
            ->method('getThemeByFullPath')
            ->with('frontend/Vendor/invalid')
            ->willReturn($theme);

        $themePathResolver = new ThemePathResolver($themeProvider);
        $themePathResolver->resolve('%theme(frontend/Vendor/invalid)%');
    }
}
