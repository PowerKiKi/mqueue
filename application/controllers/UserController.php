<?php

class UserController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->users = \mQueue\Model\UserMapper::fetchAll();
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $form = new \mQueue\Form\User();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $user = \mQueue\Model\UserMapper::insertUser($values);

                \mQueue\Model\User::setCurrent($user);

                $this->_helper->FlashMessenger('Subscription complete.');

                return $this->_helper->redirector('index', 'movie');
            }
        }

        $this->view->form = $form;
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $form = new \mQueue\Form\Login();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();

                $user = \mQueue\Model\UserMapper::findEmailPassword($values['email'], $values['password']);
                if ($user) {
                    Zend_Session::rememberMe(1 * 60 * 60 * 24 * 31 * 2); // Cookie for two months

                    \mQueue\Model\User::setCurrent($user);

                    $this->_helper->FlashMessenger('Logged in.');

                    $referrer = $values['referrer'];

                    // If we have a valid referer to one page of ourselve (except login or logout), redirect to it
                    if (mb_strpos($referrer, $this->view->serverUrl() . $this->view->baseUrl()) === 0
                            && mb_strpos($referrer, $this->view->serverUrl() . $this->view->url(['controller' => 'user', 'action' => 'login'])) !== 0
                            && mb_strpos($referrer, $this->view->serverUrl() . $this->view->url(['controller' => 'user', 'action' => 'logout'])) !== 0) {
                        return $this->_redirect($values['referrer']);
                    }

                    return $this->_helper->redirector('index', 'movie');
                }
                $this->_helper->FlashMessenger(['error' => 'Login failed.']);
            }
        } else {
            $form->setDefaults([
                'referrer' => $this->getRequest()->getServer('HTTP_REFERER'),
            ]);
        }

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        \mQueue\Model\User::setCurrent(null);
    }

    public function viewAction()
    {
        if ($this->getRequest()->getParam('id')) {
            $this->view->user = \mQueue\Model\UserMapper::find($this->getRequest()->getParam('id'));
        } else {
            $this->view->user = \mQueue\Model\User::getCurrent();
        }

        if (!$this->view->user) {
            throw new Exception($this->view->translate('User not found'));
        }

        $this->view->userActivity = $this->_helper->createPaginator(\mQueue\Model\StatusMapper::getActivityQuery($this->view->user));
    }
}
