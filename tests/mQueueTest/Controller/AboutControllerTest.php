<?php

namespace mQueueTest\Controller;

class AboutControllerTest extends AbstractControllerTestCase
{
    public function testIndexAction(): void
    {
        $params = ['action' => 'index', 'controller' => 'about', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('p', 'This project started as a personal need');
    }
}
