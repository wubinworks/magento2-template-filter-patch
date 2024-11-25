<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model\Utils;

/**
 * Safe str_replace
 */
class SafeStringReplace
{
    /**
     * In bytes
     *
     * @var int
     */
    protected $maxStringSize;

    /**
     * Constructor
     *
     * @param int $maxStringSize
     *
     * @throws \Error
     */
    public function __construct(
        int $maxStringSize = 0x02000000 // 32MB
    ) {
        if (!extension_loaded('mbstring')) {
            throw new \Error('PHP mbstring extension is not loaded.');
        }
        $this->maxStringSize = $maxStringSize;
    }

    /**
     * String replace with result length check and maximum replacement time limit
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param int $maxReplacementLimit 0 means unlimited
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function strReplace(string $search, string $replace, string $subject, int $maxReplacementLimit = 0): string
    {
        $subject = $this->_splitStringAt($subject, $search, $maxReplacementLimit);
        $sizeIncrement = $this->getStringBytes($replace) - $this->getStringBytes($search);
        $count = mb_substr_count($subject[0], $search);
        $subjectStringSize = $this->getStringBytes($subject[0]);
        if ($subjectStringSize > $this->maxStringSize
            || ($subjectStringSize + $sizeIncrement * $count) > $this->maxStringSize) {
            throw new \InvalidArgumentException(
                'Subject string is too large or contains too many parts that are needed to be replaced.'
            );
        }

        return str_replace($search, $replace, $subject[0]) . $subject[1];
    }

    /**
     * Get string size in bytes
     *
     * @param string $str
     * @return int
     */
    protected function getStringBytes(string $str): int
    {
        return mb_strlen($str, '8bit');
    }

    /**
     * Split string into 2 strings at Nth search
     *
     * @param string $haystack
     * @param string $needle
     * @param int $n
     * @return array
     */
    protected function _splitStringAt(string $haystack, string $needle, int $n = 1): array
    {
        $pos = $this->strposNth($haystack, $needle, 0, $n);
        if ($pos === false) {
            return [$haystack, ''];
        }
        $length = $pos + mb_strlen($needle);
        return [mb_substr($haystack, 0, $length), mb_substr($haystack, $length)];
    }

    /**
     * Get position in string at Nth occurrence
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @param int $n
     *
     * @return int|bool
     */
    protected function strposNth(string $haystack, string $needle, int $offset = 0, int $n = 1)
    {
        $pos = false;
        for ($i = 0; $i < $n; $i++) {
            $pos = mb_strpos($haystack, $needle, $offset);
            if ($pos === false) {
                return false;
            } else {
                $offset = $pos + mb_strlen($needle);
            }
        }

        return $pos;
    }
}
