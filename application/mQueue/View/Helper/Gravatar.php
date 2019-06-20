<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_HtmlElement;

class Gravatar extends Zend_View_Helper_HtmlElement
{
    /**
     * Return a span with CSS class for a user on gravatar
     *
     * @param \mQueue\Model\User $user
     * @param string $size small|medium|big
     *
     * @return string
     */
    public function gravatar(\mQueue\Model\User $user, $size = 'small'): string
    {
        $attribs = [
            'class' => 'gravatar user_' . $user->id . ' ' . $size,
            'title' => $user->nickname,
        ];
        $result = '<span' . $this->_htmlAttribs($attribs) . '"></span>';

        return $result;
    }
}
