<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\TemplateFilterPatch\Plugin\Framework\Filter;

use Wubinworks\TemplateFilterPatch\Model\Escaper;

/**
 * Plugin for escaping template variables
 *
 * Patch for CVE-2022-24086
 * @link https://nvd.nist.gov/vuln/detail/cve-2022-24086
 *
 * Fixes the RCE caused by malicious user data(user input)
 * Fixes an Unintended User Data Parsing Bug
 * @link https://github.com/magento/magento2/issues/39353
 *
 * About `LegacyResolver` and template compatibility impact
 * @link https://www.wubinworks.com/template-filter-patch.html#template-compatibility
 * @link https://developer.adobe.com/commerce/frontend-core/guide/templates/email-migration/
 *
 * Official patches
 * @link https://helpx.adobe.com/security/products/magento/apsb22-12.html
 */
class Template
{
    /**
     * @var array
     */
    protected $filterInfo;

    /**
     * @var Escaper
     */
    protected $templateFilterEscaper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * Constructor
     *
     * @param Escaper $templateFilterEscaper
     * @param \Psr\Log\LoggerInterface $logger
     * @param bool $debug
     */
    public function __construct(
        Escaper $templateFilterEscaper,
        \Psr\Log\LoggerInterface $logger,
        bool $debug = false
    ) {
        $this->templateFilterEscaper = $templateFilterEscaper;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * Get template variables
     *
     * @param \Magento\Framework\Filter\Template $subject
     * @param array $variables
     * @return ?array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetVariables(
        \Magento\Framework\Filter\Template $subject,
        array $variables
    ): ?array {
        $filterKey = spl_object_hash($subject);
        $this->filterInfo[$filterKey]['variables'] = $variables;
        return null;
    }

    /**
     * Escape template variables and unescape the final rendered string output
     *
     * @param \Magento\Framework\Filter\Template $subject
     * @param callable $proceed
     * @param string $value
     * @return string
     */
    public function aroundFilter(
        \Magento\Framework\Filter\Template $subject,
        callable $proceed,
        $value
    ) {
        $filterKey = spl_object_hash($subject);
        $this->debugLog('Before', $this->filterInfo[$filterKey]['variables']);
        $escapedVariables = $this->templateFilterEscaper->escape(
            $this->filterInfo[$filterKey]['variables'],
            $this->isStrictMode($subject)
        );
        $this->debugLog('After', $escapedVariables);
        $subject->setVariables($escapedVariables);

        if (!isset($this->filterInfo[$filterKey]['depth'])) {
            $this->filterInfo[$filterKey]['depth'] = 0;
        }
        $this->filterInfo[$filterKey]['depth']++;

        $result = $proceed($value);

        $this->filterInfo[$filterKey]['depth']--;
        if ($this->filterInfo[$filterKey]['depth'] === 0) {
            $result = $this->templateFilterEscaper->unescape($result);
        }

        return $result;
    }

    /**
     * Debug log
     *
     * @param string $message
     * @param mixed $var
     * @return void
     */
    protected function debugLog(string $message, $var): void
    {
        if ($this->debug) {
            $this->logger->info(
                // phpcs:ignore
                $message . PHP_EOL . print_r($this->templateFilterEscaper->debug($var), true)
            );
        }
    }

    /**
     * Determine the strict mode
     *
     * @param \Magento\Framework\Filter\Template $filter
     * @return bool
     */
    protected function isStrictMode(\Magento\Framework\Filter\Template $filter): bool
    {
        return method_exists($filter, 'isStrictMode') ? $filter->isStrictMode() : true;
    }
}
