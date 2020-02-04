<?php

namespace mQueue\View\Helper;

use Zend_Locale;
use Zend_Registry;
use Zend_View_Helper_Abstract;

class AlternateFormats extends Zend_View_Helper_Abstract
{
    protected static $supportedFormats = [
        'rss' => [
            'name' => 'RSS',
        ],
        'csv' => [
            'name' => 'CSV',
        ],
    ];

    /**
     * Returns a string of HTML links for end-user, and also append 'alternate' links to HTML header
     *
     * @param array $formats
     * @param string $title
     *
     * @return string
     */
    public function alternateFormats(array $formats, $title = null)
    {
        $formatLinks = [];
        foreach ($formats as $format => $url) {
            // Inject format and locale parameters
            if (mb_strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url .= '&';
            }

            $url .= 'format=' . $format . '&lang=' . Zend_Registry::get(Zend_Locale::class)->getLanguage();

            $formatLinks[] = '<a class="' . $format . '" href="' . $url . '">' . self::$supportedFormats[$format]['name'] . '</a>';
            if ($title && isset(self::$supportedFormats[$format]['mime'])) {
                $this->view->headLink()->appendAlternate($url, self::$supportedFormats[$format]['mime'], $title);
            }
        }

        $result = '<p class="alternateFormats">' . $this->view->translate('Also available in:') . ' ' . implode(' | ', $formatLinks) . '</p>';

        return $result;
    }
}
