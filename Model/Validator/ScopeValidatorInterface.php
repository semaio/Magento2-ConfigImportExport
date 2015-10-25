<?php

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
