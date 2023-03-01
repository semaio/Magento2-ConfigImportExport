<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Resolver;

use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Semaio\ConfigImportExport\Exception\UnresolveableValueException;

class ThemePathResolver extends AbstractResolver
{
    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    public function __construct(ThemeProviderInterface $themeProvider)
    {
        $this->themeProvider = $themeProvider;
    }

    /**
     * Resolve the config value if it's an theme code reference.
     *
     * @param string|null $value
     * @param string|null $configPath
     *
     * @return string|null
     *
     * @throws UnresolveableValueException
     */
    public function resolve($value, $configPath = null)
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        if ($value === '%theme()%') {
            throw new UnresolveableValueException('Please specify a valid theme name.');
        }

        $themeCode = preg_replace_callback(
            '/\%theme\((?)([a-zA-Z0-9\/\_]{1,})\)\%/',
            function ($matches) {
                return $matches[1];
            },
            $value
        );

        $theme = $this->themeProvider->getThemeByFullPath($themeCode);
        if ($theme instanceof ThemeInterface && $theme->getId()) {
            return (string) $theme->getId();
        }

        throw new UnresolveableValueException(sprintf('Could not find theme with given value.', $themeCode));
    }

    /**
     * @inheritDoc
     */
    public function supports($value, $configPath = null): bool
    {
        return 0 === strncmp((string) $value, '%theme', \strlen('%theme'));
    }
}
