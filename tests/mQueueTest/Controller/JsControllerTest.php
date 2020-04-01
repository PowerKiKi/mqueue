<?php

namespace mQueueTest\Controller;

class JsControllerTest extends AbstractControllerTestCase
{
    public function testRemoteJsAction(): void
    {
        $params = ['action' => 'remote.js', 'controller' => 'js', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertHeaderContains('Content-Type', 'application/javascript');
        $this->assertContentContains('jquery');
        $this->assertContentContains('mqueue');
    }

    public function testMqueueUserJsAction(): void
    {
        $params = ['action' => 'mqueue-user.js', 'controller' => 'js', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertHeaderContains('Content-Type', 'application/javascript');
        $this->assertContentContains('document.evaluate');
    }
}
