<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_HeadLink;

class HeadLink extends Zend_View_Helper_HeadLink
{
    /**
     * Override parent to inject the last modified time of file.
     * This avoid browser cache and force reloading when the file changed.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (mb_strpos($method, 'Stylesheet')) {
            $args[0] = $this->view->cacheStamp($args[0]);
        }

        return parent::__call($method, $args);
    }
}
