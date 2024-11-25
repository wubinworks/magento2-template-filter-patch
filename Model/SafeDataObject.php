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
 * Safe DataObject with getUrl method
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
// phpcs:ignore Magento2.PHP.FinalImplementation.FoundFinal
final class SafeDataObject extends DataObject implements NoninterceptableInterface
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
    public function addData($arr)
    {
    }

    public function setData($key, $value = null)
    {
    }

    public function unsetData($key = null)
    {
    }

    public function setDataUsingMethod($key, $args = [])
    {
    }

    public function getDataUsingMethod($key, $args = null)
    {
    }

    public function toXml($keys = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
    }

    public function convertToXml($arrAttributes = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
    }

    public function toJson($keys = [])
    {
    }

    public function convertToJson($keys = [])
    {
    }

    public function toString($format = '')
    {
    }

    public function serialize($keys = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
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
