<?php
/**
 * Copyright © 2015 Rouven Alexander Rieker - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Validator;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ScopeValidator
 *
 * @package Semaio\ConfigImportExport\Model\Validator
 */
class ScopeValidator implements ScopeValidatorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Validates the given scope and scope id
     *
     * @param string $scope   Scope
     * @param int    $scopeId Scope ID
     * @return bool
     */
    public function validate($scope, $scopeId)
    {
        $valid = true;
        if ($scope === 'default') { // Default Store only valid for id 0
            if ($scopeId !== 0) {
                $valid = false;
            }
        } elseif ($scope === 'websites') { // Check if website with id exists
            $valid = $this->isValidWebsiteId($scopeId);
        } elseif ($scope === 'stores') { // Check if store with id exists
            $valid = $this->isValidStoreId($scopeId);
        }

        return $valid;
    }

    /**
     * Check if the given website id is a valid website id
     *
     * @param int $websiteId Website ID
     * @return bool
     */
    private function isValidWebsiteId($websiteId)
    {
        $websites = $this->storeManager->getWebsites();
        if (array_key_exists($websiteId, $websites)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the given store id is a valid store id
     *
     * @param int $storeId Store ID
     * @return bool
     */
    private function isValidStoreId($storeId)
    {
        $stores = $this->storeManager->getStores();
        if (array_key_exists($storeId, $stores)) {
            return true;
        }

        return false;
    }
}
