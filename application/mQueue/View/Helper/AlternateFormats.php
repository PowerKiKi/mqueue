<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;
use Zend_Registry;

class AlternateFormats extends Zend_View_Helper_Abstract
{

    protected static $supportedFormats = array(
        'rss' => array(
            'name' => 'RSS',
        ),
        'csv' => array(
            'name' => 'CSV',
        ),
    );

    /**
     * Returns a string of HTML links for end-user, and also append 'alternate' links to HTML header
     * @param array $formats
     * @param string $title
     * @return string
     */
    public function alternateFormats(array $formats, $title = null)
    {
        $formatLinks = array();
        foreach ($formats as $format => $url) {

            // Inject format and locale parameters
            if (strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            
            $url .= 'format=' . $format . '&lang=' . Zend_Registry::get('Zend_Locale')->getLanguage();

            $formatLinks [] = '<a class="' . $format . '" href="' . $url . '">' . self::$supportedFormats[$format]['name'] . '</a>';
            if ($title && isset(self::$supportedFormats[$format]['mime'])) {
                $this->view->headLink()->appendAlternate($url, self::$supportedFormats[$format]['mime'], $title);
            }
        }

        $result = '<p class="alternateFormats">' . $this->view->translate('Also available in:') . ' ' . join(' | ', $formatLinks) . '</p>';

        return $result;
    }

}
