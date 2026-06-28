<?php

namespace Application\View\Helper;

use Application\Service\Sorting;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;

class Sort
{
    public function __construct(
        private readonly EscapeHtml $escapeHtml,
        private readonly EscapeHtmlAttr $escapeHtmlAttr,
        private readonly UrlParams $urlParams,
    ) {}

    /**
     * Return an HTML links to be able to sort (will typically be in table header).
     */
    public function __invoke(string $label, string $column, Sorting $sorting, ?array $additionalParameters = null): string
    {
        if ($additionalParameters === null) {
            $additionalParameters = [];
        }

        $orders = ['desc' => 'asc', 'asc' => 'desc'];
        $selectedSortOrder = $sorting->selectedOrder;
        if (!in_array($sorting->selectedOrder, $orders, true)) {
            $selectedSortOrder = reset($orders);
        }

        $url = ($this->urlParams)(array_merge($additionalParameters, ['sort' => $column, 'sortOrder' => $orders[$selectedSortOrder]]));
        $result = '<a class="sort ' . ($column === $sorting->selectedKey ? $selectedSortOrder : '') . '" title="' . ($this->escapeHtmlAttr)(_tr('Sort by "%label%"', ['label' => $label])) . '" href="' . $url . '">' . ($this->escapeHtml)($label) . '</a>';

        return $result;
    }
}
