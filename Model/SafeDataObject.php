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
use Magento\Store\Model\StoreManagerInterface;
use Wubinworks\TemplateFilterPatch\Traits\GetUrlTrait;

/**
 * Safe DataObject with getUrl method
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.FinalImplementation)
 */
// phpcs:ignore Magento2.PHP.FinalImplementation.FoundFinal
final class SafeDataObject extends DataObject implements NoninterceptableInterface
{
    use GetUrlTrait;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AbstractEmailTemplate
     */
    protected $_emailTemplate;

    /**
     * Constructor
     *
     * @param StoreManagerInterface $storeManager
     * @param ?AbstractEmailTemplate $_emailTemplate
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ?AbstractEmailTemplate $_emailTemplate = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->_emailTemplate= $_emailTemplate;
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
}
