<?php

class ActivityControllerTest extends AbstractControllerTestCase
{
    protected function assertCommonThings(array $params)
    {
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
    }

    public function testIndexAction()
    {
        $params = ['action' => 'index', 'controller' => 'activity', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertCommonThings($params);

        $this->assertQueryContentContains('h2', 'Overall activity');
    }

    public function testUserAction()
    {
        $params = ['action' => 'index', 'controller' => 'activity', 'module' => 'default', 'user' => $this->testUser->id];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertCommonThings($params);

        $this->assertQueryContentContains('h2', 'Activity for');
    }

    public function testMovieAction()
    {
        $params = ['action' => 'index', 'controller' => 'activity', 'module' => 'default', 'movie' => $this->movieData['id']];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertCommonThings($params);

        $this->assertQueryContentContains('h2', 'Activity for ' . $this->movieData['title']);
    }
}
