<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Validator;

/**
 * Interface ScopeValidatorInterface
 *
 * @package Semaio\ConfigImportExport\Model\Validator
 */
interface ScopeValidatorInterface
{
    /**
     * Validates the given scope and scope id
     *
     * @param  string $scope   Scope
     * @param  int    $scopeId Scope ID
     * @return bool
     */
    public function validate($scope, $scopeId);
}
