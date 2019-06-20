<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;

class CacheStamp extends Zend_View_Helper_Abstract
{
    /**
     * Inject the last modified time of file.
     * This avoid browser cache and force reloading when the file changed.
     *
     * @param string $fileName
     *
     * @return string
     */
    public function cacheStamp($fileName)
    {
        // In development, use non minified version
        if (APPLICATION_ENV == 'development') {
            $fileName = str_replace('/js/min/', '/js/', $fileName);
        }

        $fullPath = APPLICATION_PATH . '/../public/' . $fileName;
        if (is_file($fullPath)) {
            $fileName = $this->view->serverUrl() . $this->view->baseUrl($fileName) . '?' . filemtime($fullPath);
        }

        return $fileName;
    }
}
