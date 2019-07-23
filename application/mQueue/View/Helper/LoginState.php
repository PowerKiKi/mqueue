<?php

namespace mQueue\View\Helper;

use mQueue\Model\User;
use Zend_View_Helper_Abstract;

class LoginState extends Zend_View_Helper_Abstract
{
    /**
     * Returns a string displaying the login state of the user and buttons to login/off
     *
     * @return string
     */
    public function loginState()
    {
        $result = '<div class="loginState">';

        $user = User::getCurrent();
        if ($user) {
            $result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
                    ['controller' => 'user', 'action' => 'view', 'id' => $user->id], 'singleid', true) . '">' . $this->view->gravatar($user) . ' ' . $this->view->escape($user->nickname) . '</a> ';

            $result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
                    ['controller' => 'user', 'action' => 'logout'], 'default', true) . '">' . $this->view->translate('Logout') . '</a> ';
        } else {
            $result .= ' <a href="' . $this->view->serverUrl() . $this->view->url(
                    ['controller' => 'user', 'action' => 'new'], 'default', true) . '">' . $this->view->translate('Subscribe') . '</a> ';

            $result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
                    ['controller' => 'user', 'action' => 'login'], 'default', true) . '">' . $this->view->translate('Login') . '</a>';
        }

        return $result . '</div>';
    }
}
