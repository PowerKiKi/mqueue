<?php

use mQueue\Form\Login;
use mQueue\Model\StatusMapper;
use mQueue\Model\User;
use mQueue\Model\UserMapper;

class UserController extends Zend_Controller_Action
{
    public function init(): void
    {
        // Initialize action controller here
    }

    public function indexAction(): void
    {
        $this->view->users = UserMapper::fetchAll();
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $form = new \mQueue\Form\User();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $user = UserMapper::insertUser($values);

                User::setCurrent($user);

                $this->_helper->FlashMessenger('Subscription complete.');

                return $this->_helper->redirector('index', 'movie');
            }
        }

        $this->view->form = $form;
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $form = new Login();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();

                $user = UserMapper::findEmailPassword($values['email'], $values['password']);
                if ($user) {
                    User::setCurrent($user);

                    $this->_helper->FlashMessenger('Logged in.');

                    $referrer = $values['referrer'];

                    // If we have a valid referer to one page of ours (except login or logout), redirect to it
                    if (mb_strpos($referrer, $this->view->serverUrl() . $this->view->baseUrl()) === 0
                        && mb_strpos($referrer, $this->view->serverUrl() . $this->view->url(['controller' => 'user', 'action' => 'login'])) !== 0
                        && mb_strpos($referrer, $this->view->serverUrl() . $this->view->url(['controller' => 'user', 'action' => 'logout'])) !== 0) {
                        return $this->redirect($values['referrer']);
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

    public function logoutAction(): void
    {
        User::setCurrent(null);
    }

    public function viewAction(): void
    {
        if ($this->getRequest()->getParam('id')) {
            $this->view->user = UserMapper::find($this->getRequest()->getParam('id'));
        } else {
            $this->view->user = User::getCurrent();
        }

        if (!$this->view->user) {
            throw new Exception($this->view->translate('User not found'));
        }

        $this->view->userActivity = $this->_helper->createPaginator(StatusMapper::getActivityQuery($this->view->user));
    }
}
