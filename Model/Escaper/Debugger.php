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
        } else {
            return $input;
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
            if (is_array($item)) {
                $result[$key] = $this->debugArray($item);
            } elseif (is_object($item)) {
                $result[$key] = $this->debugObject($item);
            } else {
                $result[$key] = $item;
            }
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
        $data = $this->debugArray($input->getData());
        $typeKey = '__TYPE__DataObject__';
        if ($input instanceof AbstractEmailTemplate) {
            $typeKey = '__TYPE__AbstractTemplate__';
        }
        $data[$typeKey] = get_class($input);
        return $data;
    }
}
