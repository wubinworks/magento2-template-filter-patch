<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model\Utils;

use Magento\Framework\DataObject;

class DataObjectDeserializer
{
    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * Deserialize DataObject with maximum depth(to prevent infinite loop)
     *
     * @param DataObject $dataObj
     * @param int $maxDepth
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function deserialize(
        DataObject $dataObj,
        int $maxDepth
    ): array {
        if ($maxDepth < 1) {
            throw new \InvalidArgumentException('$maxDepth cannot be less than 1.');
        }
        $this->maxDepth = $maxDepth;
        return $this->_deserialize($dataObj);
    }

    /**
     * Deserialization
     *
     * @param DataObject $dataObj
     * @param int $currDepth
     * @return array
     */
    protected function _deserialize(
        DataObject $dataObj,
        int $currDepth = 1
    ): array {
        $data = $dataObj->getData();
        $result = [];
        foreach ($data as $key => $item) {
            if (is_scalar($item) || $item === null) {
                $result[$key] = $item;
            } elseif ($currDepth < $this->maxDepth && ($item instanceof DataObject)) {
                $result[$key] = $this->_deserialize($item, $currDepth + 1);
            }
        }

        return $result;
    }
}
