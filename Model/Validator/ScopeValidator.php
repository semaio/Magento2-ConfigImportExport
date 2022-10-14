<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Validator;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;

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
     * @param string     $scope   Scope
     * @param string|int $scopeId Scope ID
     *
     * @return bool
     */
    public function validate($scope, $scopeId)
    {
        $valid = true;

        // Default scope is only valid for store id 0
        if ($scope === 'default' && $scopeId !== 0) {
            return false;
        }

        // Check if website with given id/code exists.
        if ($scope === 'websites') {
            return $this->isValidWebsiteId($scopeId);
        }

        // Check if store with given id/code exists.
        if ($scope === 'stores') {
            return $this->isValidStoreId($scopeId);
        }

        return true;
    }

    /**
     * Check if the given website id is a valid website id
     *
     * @param string|int $websiteId Website ID
     *
     * @return bool
     */
    private function isValidWebsiteId($websiteId)
    {
        $websites = $this->storeManager->getWebsites();
        if (array_key_exists($websiteId, $websites)) {
            return true;
        }

        // Don't bother checking website codes on numeric input.
        if (is_numeric($websiteId)) {
            return false;
        }

        /** @var Website $website */
        foreach ($websites as $website) {
            if ($website->getCode() == $websiteId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the given store id is a valid store id
     *
     * @param string|int $storeId Store ID
     *
     * @return bool
     */
    private function isValidStoreId($storeId)
    {
        $stores = $this->storeManager->getStores(true);
        if (array_key_exists($storeId, $stores)) {
            return true;
        }

        // Don't bother checking store codes on numeric input.
        if (is_numeric($storeId)) {
            return false;
        }

        /** @var Store $store */
        foreach ($stores as $store) {
            if ($store->getCode() == $storeId) {
                return true;
            }
        }

        return false;
    }
}
