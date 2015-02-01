<?php

class UserControllerTest extends AbstractControllerTestCase
{

    protected $idUser = null;
    protected $newUserData = [
        'nickname' => 'new test user',
        'email' => 'new_valid@email.org',
        'password' => 'superpassword',
    ];

    public function tearDown()
    {
        $user = \mQueue\Model\UserMapper::findEmailPassword($this->newUserData['email'], $this->newUserData['password']);
        if ($user)
            $user->delete();

        parent::tearDown();
    }

    public function testIndexAction()
    {
        $params = ['action' => 'index', 'controller' => 'user', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('h2', 'Users list');
    }

    public function testNewAction()
    {
        // First, query to display form
        $params = ['action' => 'new', 'controller' => 'user', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('h2', 'Create new user');
        $this->assertQueryContentContains('form', 'Subscribe');

        // Find out csrf value to be re-used in POST
        $body = $this->getResponse()->getBody();
        preg_match('/name="csrf" value="([^"]+)"/', $body, $m);
        $csrf = $m[1];

        // Reset everything
        $this->resetRequest();
        $this->resetResponse();

        // Prepare POST query
        $this->newUserData['csrf'] = $csrf;
        $this->request->setMethod('POST')
                ->setPost($this->newUserData);

        // Subscribe new test user
        $this->dispatch($url);

        $this->assertRedirectTo('/movie', 'succesfull subscription redirect to movie list');
    }

    public function testLoginAction()
    {

        $this->assertNull(\mQueue\Model\User::getCurrent(), 'at first we are not logged in');

        // Create test user
        $this->testNewAction();

        $this->assertNotNull(\mQueue\Model\User::getCurrent(), 'after subscription, we are automatically logged in');

        // Reset everything
        $this->resetRequest();
        $this->resetResponse();

        $params = ['action' => 'logout', 'controller' => 'user', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        $this->assertNull(\mQueue\Model\User::getCurrent(), 'after logged out, we are logged out');

        // Reset everything
        $this->resetRequest();
        $this->resetResponse();

        // Prepare POST query
        $this->request->setMethod('POST')
                ->setPost($this->newUserData);

        $params = ['action' => 'login', 'controller' => 'user', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        $this->assertNotNull(\mQueue\Model\User::getCurrent(), 'after login, we are login');
    }

}
