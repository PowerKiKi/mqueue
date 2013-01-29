<?php

require_once 'PHPUnit/Framework/TestCase.php';

class UserControllerTest extends AbstractControllerTestCase
{
	protected $idUser = null;
	protected $userData = array(
				  'nickname' => 'test user',
				  'email' => 'valid@email.org',
				  'password' => 'superpassword',
			  );
	
	public function tearDown()
	{
		$user = Default_Model_UserMapper::findEmailPassword($this->userData['email'], $this->userData['password']);
		if ($user)
			$user->delete();
		;
	}

	public function testIndexAction()
	{
        $params = array('action' => 'index', 'controller' => 'user', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
        $this->assertQueryContentContains(
            'h2',
            'Users list'
            );
	}

	public function testNewAction()
	{
		// First, query to display form
        $params = array('action' => 'new', 'controller' => 'user', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
        $this->assertQueryContentContains(
            'h2',
            'Create new user'
            );
        $this->assertQueryContentContains(
            'form',
            'Subscribe'
            );

		// Find out csrf value to be re-used in POST
		$body = $this->getResponse()->getBody();
		preg_match('/name="csrf" value="([^"]+)"/', $body, $m);
		$csrf = $m[1];
		
		// Reset everything
		$this->resetRequest();
		$this->resetResponse();
		
		// Prepare POST query
		$this->userData['csrf'] = $csrf;
		$this->request->setMethod('POST')
			  ->setPost($this->userData);
		
		// Subscribe new test user
        $this->dispatch($url);
        
		$this->assertRedirectTo('/movie', 'succesfull subscription redirect to movie list');
	}
	
	public function testLoginAction()
	{
		
		$this->assertNull(Default_Model_User::getCurrent(), 'at first we are not logged in');
		
		// Create test user
		$this->testNewAction();
		
		$this->assertNotNull(Default_Model_User::getCurrent(), 'after subscription, we are automatically logged in');
		
		// Reset everything
		$this->resetRequest();
		$this->resetResponse();
		
        $params = array('action' => 'logout', 'controller' => 'user', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

		$this->assertNull(Default_Model_User::getCurrent(), 'after logged out, we are logged out');
		
		// Reset everything
		$this->resetRequest();
		$this->resetResponse();
		
		
		// Prepare POST query
		$this->request->setMethod('POST')
			  ->setPost($this->userData);
		
        $params = array('action' => 'login', 'controller' => 'user', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

		$this->assertNotNull(Default_Model_User::getCurrent(), 'after login, we are login');
		
		
	}

}
