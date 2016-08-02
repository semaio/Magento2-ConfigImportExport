<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Converter;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ScopeConverter
 *
 * @package Semaio\ConfigImportExport\Model\Converter
 */
class ScopeConverter implements ScopeConverterInterface
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
     * Converts a string scope to integer scope id if needed
     *
     * @param string|int   $scopeId Scope ID
     * @param string|mixed $scope   Scope
     * @return int
     */
    public function convert($scopeId, $scope)
    {
        if (is_numeric($scopeId)) {
            return $scopeId;
        }

        if ($scope == 'stores') {
            $store = $this->storeManager->getStore($scopeId);
            if ($store->getId() > 0) {
                return $store->getId();
            }
        }

        if ($scope == 'websites') {
            $website = $this->storeManager->getWebsite($scopeId);
            if ($website->getId() > 0) {
                return $website->getId();
            }
        }

        return $scopeId;
    }
}
