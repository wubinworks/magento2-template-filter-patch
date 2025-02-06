<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model\Escaper;

use Magento\Framework\DataObject;
use Magento\Email\Model\AbstractTemplate as AbstractEmailTemplate;

/**
 * Debugger for nested array and DataObject
 */
class Debugger
{
    /**
     * @var int
     */
    protected $maxDataObjectDebugDepth = 2;

    /**
     * Debug mixed input
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function debug($input)
    {
        if (is_array($input)) {
            return $this->debugArray($input);
        } elseif (is_object($input)) {
            return $this->debugObject($input);
        } elseif (is_scalar($input) || $input === null) {
            return $input;
        } else {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            return gettype($input);
        }
    }

    /**
     * Debug array
     *
     * @param array $input
     *
     * @return array
     */
    protected function debugArray(array $input): array
    {
        $result = [];
        foreach ($input as $key => $item) {
            $result[$key] = $this->debug($item);
        }
        return $result;
    }

    /**
     * Debug object
     *
     * @param object $input
     *
     * @return array|string
     */
    protected function debugObject($input)
    {
        if ($input instanceof DataObject) {
            return $this->debugDataObject($input);
        } else {
            return get_class($input);
        }
    }

    /**
     * Debug DataObject
     *
     * @param DataObject $input
     *
     * @return array
     */
    protected function debugDataObject(DataObject $input): array
    {
        return $this->_debugDataObject($input);
    }

    /**
     * Debug DataObject
     *
     * @param DataObject $dataObj
     * @param int $currDepth
     * @return array
     */
    protected function _debugDataObject(DataObject $dataObj, int $currDepth = 1): array
    {
        $data = $dataObj->getData();
        $result = [];
        foreach ($data as $key => $item) {
            if ($item instanceof DataObject) {
                if ($currDepth < $this->maxDataObjectDebugDepth) {
                    $result[$key] = $this->_debugDataObject($item, $currDepth + 1);
                } else {
                    $result[$key] = get_class($item);
                }
            } else {
                $result[$key] = $this->debug($item);
            }
        }

        $typeKey = '__TYPE__DataObject__';
        if ($dataObj instanceof AbstractEmailTemplate) {
            $typeKey = '__TYPE__AbstractTemplate__';
        }
        $data[$typeKey] = get_class($dataObj);

        return $result;
    }
}
