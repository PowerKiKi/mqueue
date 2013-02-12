<?php

require_once 'PHPUnit/Framework/TestCase.php';

class MovieControllerTest extends AbstractControllerTestCase
{

	public function testIndexAction()
	{
        $params = array('action' => 'index', 'controller' => 'movie', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
        $this->assertQueryContentContains(
            'legend',
            'Filter'
            );
		
        $this->assertQueryContentContains(
            'th a',
            'Title'
            );
	}
    
	public function testAddAction()
	{
        $params = array('action' => 'add', 'controller' => 'movie', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
        $this->assertQueryContentContains(
            'label',
            'IMDb url or id'
            );
		
        $this->assertQueryContentContains(
            '.tips',
            'learn how to add'
            );
	}
    
	public function testImportAction()
	{
        $params = array('action' => 'import', 'controller' => 'movie', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
        $this->assertQueryContentContains(
            'label',
            'Vote History'
            );
		
        $this->assertQueryContentContains(
            'label',
            'Minimum for favorite'
            );
		
        $this->assertQueryContentContains(
            'label',
            'Minimum for excellent'
            );
		
        $this->assertQueryContentContains(
            'label',
            'Minimum for ok'
            );
		
        $this->assertQueryContentContains(
            '.tips',
            'learn how to add'
            );
	}

}
