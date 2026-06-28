<?php

namespace Application\View\Helper;

use Mezzio\LaminasView\UrlHelper;

class UrlParams
{
    public function __construct(
        private readonly UrlHelper $url,
    ) {}

    private bool $escapeParams;

    /**
     * Returns the current page URL with specified GET parameters.
     *
     * @param bool $escapeParams if false '&' characters will not be escaped
     */
    public function __invoke(array $params, bool $escapeParams = true): string
    {
        $this->escapeParams = $escapeParams;

        return ($this->url)() . '?' . $this->flatten($params);
    }

    /**
     * Flatten a recursive array in GET parameters (same as HTML form send GET request).
     */
    private function flatten(array $params, string $result = '', string $previousName = ''): string
    {
        foreach ($params as $key => $value) {
            if ($previousName) {
                $name = $previousName . '[' . $key . ']';
            } else {
                $name = $key;
            }

            if (is_array($value)) {
                $result = $this->flatten($value, $result, $name);
            } else {
                if ($result) {
                    $result .= $this->escapeParams ? '&amp;' : '&';
                }

                $result .= $name . '=' . urlencode($value ?? '');
            }
        }

        return $result;
    }
}
