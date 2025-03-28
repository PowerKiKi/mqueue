<?php

namespace mQueueTest\Controller;

class MovieControllerTest extends AbstractControllerTestCase
{
    public function testIndexAction(): void
    {
        $params = ['action' => 'index', 'controller' => 'movie', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('legend', 'Filter');
        $this->assertQueryContentContains('th a', 'Title');
    }

    public function testAddAction(): void
    {
        $params = ['action' => 'add', 'controller' => 'movie', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('label', 'IMDb url or id');
        $this->assertQueryContentContains('.tips', 'learn how to add');
    }
}
