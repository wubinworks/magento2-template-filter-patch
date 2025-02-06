<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model\Escaper;

use Magento\Framework\DataObject;
use Magento\Email\Model\AbstractTemplate as AbstractEmailTemplate;
use Wubinworks\TemplateFilterPatch\Model\Utils\SafeStringReplace;
use Wubinworks\TemplateFilterPatch\Model\Utils\DataObjectDeserializer;
use Wubinworks\TemplateFilterPatch\Model\SafeDataObject;
use Wubinworks\TemplateFilterPatch\Model\SafeDataObjectFactory;
use Wubinworks\TemplateFilterPatch\Model\SafeEmailTemplate;
use Wubinworks\TemplateFilterPatch\Model\SafeEmailTemplateFactory;

/**
 * Filter that can perform deep filtering
 */
class Filter
{
    /**
     * Object class(and its child class) that will be removed
     *
     * @var string[]
     */
    protected $prohibitedTypes;

    /**
     * @var SafeStringReplace
     */
    protected $safeStringReplace;

    /**
     * @var DataObjectDeserializer
     */
    protected $dataObjectDeserializer;

    /**
     * @var bool
     */
    protected $strictMode = true;

    /**
     * @var SafeDataObjectFactory
     */
    protected $safeDataObjectFactory;

    /**
     * @var SafeEmailTemplateFactory
     */
    protected $safeEmailTemplateFactory;

    /**
     * Constructor
     *
     * @param SafeStringReplace $safeStringReplace
     * @param DataObjectDeserializer $dataObjectDeserializer
     * @param SafeDataObjectFactory $safeDataObjectFactory
     * @param SafeEmailTemplateFactory $safeEmailTemplateFactory
     * @param string[] $prohibitedTypes
     */
    public function __construct(
        SafeStringReplace $safeStringReplace,
        DataObjectDeserializer $dataObjectDeserializer,
        SafeDataObjectFactory $safeDataObjectFactory,
        SafeEmailTemplateFactory $safeEmailTemplateFactory,
        array $prohibitedTypes = []
    ) {
        $this->safeStringReplace = $safeStringReplace;
        $this->dataObjectDeserializer = $dataObjectDeserializer;
        $this->safeDataObjectFactory = $safeDataObjectFactory;
        $this->safeEmailTemplateFactory = $safeEmailTemplateFactory;
        $this->prohibitedTypes = $prohibitedTypes;
    }

    /**
     * Check if object is prohibited types
     *
     * @param object $obj
     * @return bool
     */
    protected function isProhibitedObject($obj): bool
    {
        if (is_object($obj)) {
            foreach ($this->prohibitedTypes as $type) {
                if ($obj instanceof $type) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if variable is bool, int, float, null
     *
     * @param mixed $var
     * @return bool
     */
    protected function isSafeType($var): bool
    {
        return !is_string($var) && (is_scalar($var) || $var === null);
    }

    /**
     * Process mixed input. Return empty array if input is filtered
     *
     * @param mixed $input
     * @param string $search
     * @param string $replace
     *
     * @return mixed Possible returns include: SafeDataObject|AbstractEmailTemplate|array
     */
    public function process($input, string $search, string $replace)
    {
        if (is_array($input)) {
            return $this->processArray($input, $search, $replace);
        } elseif (is_object($input)) {
            return $this->processObject($input, $search, $replace);
        } elseif (is_string($input)) {
            return $this->processString($input, $search, $replace);
        } elseif ($this->isSafeType($input)) {
            return $input;
        } else {
            return [];
        }
    }

    /**
     * Process array
     *
     * @param array $input
     * @param string $search
     * @param string $replace
     *
     * @return array
     */
    protected function processArray(array $input, string $search, string $replace): array
    {
        $result = [];
        foreach ($input as $key => $item) {
            $processed = $this->process($item, $search, $replace);
            // Don't add empty array
            if ($processed !== []) {
                $result[$key] = $processed;
            }
        }

        return $result;
    }

    /**
     * Process object
     *
     * @param object $input
     * @param string $search
     * @param string $replace
     *
     * @return SafeDataObject|AbstractEmailTemplate|array
     *
     * @throws \InvalidArgumentException
     */
    protected function processObject($input, string $search, string $replace)
    {
        if (!is_object($input)) {
            throw new \InvalidArgumentException('$input must be an Object.');
        }
        if ($this->isProhibitedObject($input)) {
            return [];
        } elseif ($input instanceof AbstractEmailTemplate) {
            return $this->createSafeEmailTemplate($input);
        } elseif ($input instanceof DataObject) {
            return $this->processDataObject($input, $search, $replace);
        } else {
            return [];
        }
    }

    /**
     * Process DataObject
     *
     * @param DataObject $input
     * @param string $search
     * @param string $replace
     *
     * @return SafeDataObject
     */
    protected function processDataObject(DataObject $input, string $search, string $replace): SafeDataObject
    {
        $data = $this->dataObjectDeserializer->deserialize($input, 2);
        $data = $this->processArray($data, $search, $replace);
        return $this->safeDataObjectFactory->create(['data' => $data]);
    }

    /**
     * Create safe email template. A wrapper for calling `getUrl` method.
     *
     * @param AbstractEmailTemplate $input
     *
     * @return SafeDataObject|AbstractEmailTemplate
     *
     * @throws \InvalidArgumentException
     */
    protected function createSafeEmailTemplate(AbstractEmailTemplate $input): DataObject
    {
        $data = $this->dataObjectDeserializer->deserialize($input, 1);

        if (class_exists(\Magento\Framework\Filter\VariableResolver\LegacyResolver::class)
            && !$this->isStrictMode()) {
            // LegacyResolver will be used. Need return DataObject instead of AbstractEmailTemplate
            return $this->safeDataObjectFactory->create([
                '_emailTemplate' => $input,
                'data' => $data
            ]);
        } else {
            return $this->safeEmailTemplateFactory->create([
                '_emailTemplate' => $input,
                'data' => $data
            ]);
        }
    }

    /**
     * Process string
     *
     * @param string $input
     * @param string $search
     * @param string $replace
     *
     * @return string
     */
    protected function processString(string $input, string $search, string $replace): string
    {
        return $this->safeStringReplace->strReplace($search, $replace, $input);
    }

    /**
     * Set strict mode
     *
     * @param bool $strictMode
     * @return bool The previous mode
     */
    public function setStrictMode(bool $strictMode): bool
    {
        $current = $this->strictMode;
        $this->strictMode = $strictMode;

        return $current;
    }

    /**
     * Is strict mode
     *
     * @return bool
     */
    public function isStrictMode(): bool
    {
        return $this->strictMode;
    }
}
