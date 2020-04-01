<?php

namespace mQueueTest\Controller;

class IndexControllerTest extends AbstractControllerTestCase
{
    public function testIndexAction(): void
    {
        $params = ['action' => 'index', 'controller' => 'index', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertRedirectTo('/activity');
    }
}
