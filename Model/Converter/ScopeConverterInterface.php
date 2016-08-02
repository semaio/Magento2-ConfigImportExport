<?php
/**
 * Copyright © 2016 Christian Münch
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Converter;

interface ScopeConverterInterface

{
    /**
     * Converts a string scope to integer scope id if needed
     *
     * @param string|int $scopeId
     * @param $scope
     * @return int
     */
    public function convert($scopeId, $scope);
}