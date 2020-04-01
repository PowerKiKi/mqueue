<?php

namespace mQueueTest\Controller;

class FaqControllerTest extends AbstractControllerTestCase
{
    public function testIndexAction(): void
    {
        $params = ['action' => 'index', 'controller' => 'faq', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('li', 'Create an account');
        $this->assertQueryContentContains('ul.statusHelp li', 'I want to see this movie');
    }
}
