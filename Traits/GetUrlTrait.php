<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Traits;

use Magento\Framework\DataObject;
use Magento\Store\Model\Store;

/**
 * getUrl trait
 */
trait GetUrlTrait
{
    /**
     * Get URL by store
     *
     * @param Store|DataObject|int|string|null $store
     * @param string $route
     * @param array $params
     *
     * @return string|null
     */
    public function getUrl($store, $route = '', $params = []): ?string
    {
        if (!$this->_emailTemplate) {
            throw new \LogicException('Email template is not set.');
        }

        if ($store instanceof DataObject) {
            $storeId = $store->getData('store_id');
        } else {
            $storeId = $store;
        }

        try {
            /** @var Store $store */
            $store = $this->storeManager->getStore($storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $this->_emailTemplate->getUrl($store, $route, $params);
    }
}
