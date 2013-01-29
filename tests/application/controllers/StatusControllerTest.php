<?php

require_once 'PHPUnit/Framework/TestCase.php';

class StatusControllerTest extends AbstractControllerTestCase
{

	public function testIndexAction()
	{
        $params = array('action' => 'index', 'controller' => 'Faq', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
//		$this->assertRedirectTo('user/login');
		
        $this->assertQueryContentContains(
            'li',
            'Create an account'
            );
		
        $this->assertQueryContentContains(
            'ul.statusHelp li',
            'I want to see this movie'
            );
	}

}
