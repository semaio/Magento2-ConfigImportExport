<?php
/**
 * Copyright © semaio GmbH. All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Semaio\ConfigImportExport\Model\Processor;

use Magento\Config\Model\ResourceModel\Config\Data\Collection as ConfigDataCollection;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigDataCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Semaio\ConfigImportExport\Model\File\Writer\WriterInterface;

class ExportProcessor extends AbstractProcessor implements ExportProcessorInterface
{
    /**
     * @var ConfigDataCollectionFactory
     */
    private $configDataCollectionFactory;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var string|null
     */
    private $include = null;

    /**
     * @var string|null
     */
    private $includeScope = null;

    /**
     * @var string|null
     */
    private $exclude = null;

    /**
     * @param ConfigDataCollectionFactory $configDataCollectionFactory
     */
    public function __construct(ConfigDataCollectionFactory $configDataCollectionFactory)
    {
        $this->configDataCollectionFactory = $configDataCollectionFactory;
    }

    /**
     * Process configuration export.
     *
     * @return void
     */
    public function process()
    {
        /** @var ConfigDataCollection $collection */
        $collection = $this->configDataCollectionFactory->create();
        $collection->setOrder('path', SortOrder::SORT_ASC);
        $collection->setOrder('scope', SortOrder::SORT_ASC);
        $collection->setOrder('scope_id', SortOrder::SORT_ASC);

        // Filter collection by includes
        if (null !== $this->include) {
            $includes = explode(',', $this->include);
            $orWhere = [];
            foreach ($includes as $singlePath) {
                $singlePath = trim($singlePath);
                if (!empty($singlePath)) {
                    $orWhere[] = $collection->getConnection()->quoteInto('`path` LIKE ?', $singlePath . '%');
                }
            }
            if (count($orWhere) > 0) {
                $collection->getSelect()->where(implode(' OR ', $orWhere));
            }
        }

        // Filter collection by scope
        if (null !== $this->includeScope) {
            $includeScopes = explode(',', $this->includeScope);
            $orWhere = [];
            foreach ($includeScopes as $singlePath) {
                $singlePath = trim($singlePath);
                if (!empty($singlePath)) {
                    $orWhere[] = $collection->getConnection()->quoteInto('`scope` like ?', $singlePath);
                }
            }
            if (count($orWhere) > 0) {
                $collection->getSelect()->where(implode(' OR ', $orWhere));
            }
        }

        // Filter collection by excludes
        if (null !== $this->exclude) {
            $excludes = explode(',', $this->exclude);
            foreach ($excludes as $singleExclude) {
                $singleExclude = trim($singleExclude);
                if (!empty($singleExclude)) {
                    $collection->getSelect()->where('`path` NOT LIKE ?', $singleExclude . '%');
                }
            }
        }

        $exportData = [];
        foreach ($collection as $item) {
            $data = $item->getData();
            unset($data['config_id']);
            ksort($data);
            $exportData[] = $data;
        }

        if (count($exportData) == 0) {
            $this->getOutput()->writeln('<error>No export data found.</error>');

            return;
        }

        $this->writer->write($exportData);
    }

    /**
     * @param WriterInterface $writer
     *
     * @return void
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param string|null $include
     *
     * @return void
     */
    public function setInclude($include)
    {
        $this->include = $include;
    }

    /**
     * @param string|null $includeScope
     *
     * @return void
     */
    public function setIncludeScope($includeScope)
    {
        $this->includeScope = $includeScope;
    }

    /**
     * @param string|null $exclude
     *
     * @return void
     */
    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
    }
}
