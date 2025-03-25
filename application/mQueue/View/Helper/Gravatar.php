<?php

namespace mQueue\View\Helper;

use mQueue\Model\User;
use Zend_View_Helper_HtmlElement;

class Gravatar extends Zend_View_Helper_HtmlElement
{
    /**
     * Return a span with CSS class for a user on gravatar.
     *
     * @param string $size small|medium|big
     */
    public function gravatar(User $user, $size = 'small'): string
    {
        $attribs = [
            'class' => 'gravatar user_' . $user->id . ' ' . $size,
            'title' => $user->nickname,
        ];
        $result = '<span' . $this->_htmlAttribs($attribs) . '"></span>';

        return $result;
    }
}
