<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManager\NoninterceptableInterface;
use Magento\Email\Model\AbstractTemplate as AbstractEmailTemplate;
use Magento\Store\Model\Store;
use Wubinworks\TemplateFilterPatch\Model\StoreUrl;

/**
 * Safe email template object
 */
// phpcs:ignore Magento2.PHP.FinalImplementation.FoundFinal
final class SafeEmailTemplate extends AbstractEmailTemplate implements NoninterceptableInterface
{
    /**
     * @var StoreUrl
     */
    protected $storeUrlBuilder;

    /**
     * Constructor
     *
     * @param StoreUrl $storeUrlBuilder
     * @param array $data
     */
    public function __construct(
        StoreUrl $storeUrlBuilder,
        array $data = []
    ) {
        $this->storeUrlBuilder= $storeUrlBuilder;
        $this->_data = $data;
    }

    // phpcs:disable Magento2
    protected function getFilterFactory()
    {
    }

    public function getType()
    {
    }
    // phpcs:enable

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
        return $this->storeUrlBuilder->getUrl($store, $route, $params);
    }
}
