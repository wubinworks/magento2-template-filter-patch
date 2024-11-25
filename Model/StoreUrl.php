<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * For `this.getUrl()`
 */
class StoreUrl
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get URL by store
     *
     * @param Store|DataObject|int|string|null $inputStore
     * @param string $route
     * @param array $params
     *
     * @return string|null
     */
    public function getUrl($inputStore, $route = '', $params = []): ?string
    {
        if ($inputStore instanceof DataObject) {
            $storeId = $inputStore->getData('store_id');
        } else {
            $storeId = $inputStore;
        }

        try {
            /** @var Store $store */
            $store = $this->storeManager->getStore($storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $store->getUrl($route, $params);
    }
}
