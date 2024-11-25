<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Model;

use Wubinworks\TemplateFilterPatch\Model\Escaper\Filter;
use Wubinworks\TemplateFilterPatch\Model\Escaper\Debugger;
use Wubinworks\TemplateFilterPatch\Model\Utils\SafeStringReplace;

/**
 * Escape strings inside nested arrays and objects
 * Don't inject this class directly, use Factory or create virtual type.
 */
class Escaper
{
    /**
     * @var string
     */
    protected $search;

    /**
     * @var SafeStringReplace
     */
    protected $safeStringReplace;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Debugger
     */
    protected $debugger;

    /**
     * 128 bit
     *
     * @var int
     */
    protected $randomness = 16;

    /**
     * @var ?string
     */
    protected $random = null;

    /**
     * Constructor
     *
     * @param Filter $filter
     * @param Debugger $debugger
     * @param SafeStringReplace $safeStringReplace
     * @param string $search
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Filter $filter,
        Debugger $debugger,
        SafeStringReplace $safeStringReplace,
        string $search = ''
    ) {
        if ($search === '') {
            throw new \InvalidArgumentException(
                'Parameter $search should be a non-empty string. '
                . 'Class ' . get_class($this) . ' needs to be created by Factory or virtual type.'
            );
        }
        $this->search = $search;
        $this->filter = $filter;
        $this->debugger = $debugger;
        $this->safeStringReplace = $safeStringReplace;
    }

    /**
     * Escape array input
     *
     * @param array $input
     * @param bool $strictMode For <= 2.4.3-p1. Needs to be the same with template filter
     * @return array
     */
    public function escape(array $input, bool $strictMode = true): array
    {
        $this->filter->setStrictMode($strictMode);
        return $this->filter->process($input, $this->search, $this->getRandom());
    }

    /**
     * Unescape input string
     *
     * @param string $input
     * @return string
     */
    public function unescape($input)
    {
        if (is_string($input)) {
            return $this->safeStringReplace->strReplace($this->getRandom(), $this->search, $input);
        } else {
            return $input;
        }
    }

    /**
     * Generate random string
     *
     * @return string
     */
    protected function getRandom(): string
    {
        if ($this->random === null) {
            $this->random = bin2hex(random_bytes($this->randomness));
        }
        return $this->random;
    }

    /**
     * Dump input. DataObject will be converted to array
     *
     * @param mixed $input
     * @return mixed
     */
    public function debug($input)
    {
        return $this->debugger->debug($input);
    }
}
