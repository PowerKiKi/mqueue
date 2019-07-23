<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;

class Footer extends Zend_View_Helper_Abstract
{
    /**
     * Returns the website footer
     *
     * @return string
     */
    public function footer()
    {
        $result = '';

        $result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
                ['controller' => 'about'], 'default', true) . '">' . $this->view->translate('about mQueue') . '</a> ';

        return $result;
    }
}
