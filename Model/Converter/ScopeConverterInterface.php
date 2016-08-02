<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Converter;

/**
 * Interface ScopeConverterInterface
 *
 * @package Semaio\ConfigImportExport\Model\Converter
 */
interface ScopeConverterInterface
{
    /**
     * Converts a string scope to integer scope id if needed
     *
     * @param string|int   $scopeId Scope ID
     * @param string|mixed $scope   Scope
     * @return int
     */
    public function convert($scopeId, $scope);
}
