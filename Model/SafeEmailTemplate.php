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
 * Safe email template object
 *
 * @SuppressWarnings(PHPMD.FinalImplementation)
 */
// phpcs:ignore Magento2.PHP.FinalImplementation.FoundFinal
final class SafeEmailTemplate extends AbstractEmailTemplate implements NoninterceptableInterface
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
    protected function getFilterFactory()
    {
    }

    public function getType()
    {
    }
    // phpcs:enable
}
