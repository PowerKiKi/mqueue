<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;

class UrlParams extends Zend_View_Helper_Abstract
{
    private $escapeParams;

    /**
     * Returns the current page URL with specified GET parameters.
     *
     * @param array $params
     * @param bool $escapeParams if false '&' characters will not be escaped
     *
     * @return string
     */
    public function urlParams(array $params, $escapeParams = true)
    {
        $this->escapeParams = $escapeParams;

        return $this->view->serverUrl() . $this->view->url() . '?' . $this->flatten($params);
    }

    /**
     * Flatten a recursive array in GET parameters (same as HTML form send GET request)
     *
     * @param array $params
     * @param string $result
     * @param string $previousName
     *
     * @return string
     */
    private function flatten(array $params, $result = '', $previousName = null): string
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

                $result .= $name . '=' . urlencode($value);
            }
        }

        return $result;
    }
}
