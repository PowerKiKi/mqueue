<?php

namespace mQueueTest\Controller;

class StatusControllerTest extends AbstractControllerTestCase
{
    public function testIndexAction(): void
    {
        $params = ['action' => 'index', 'controller' => 'status', 'module' => 'default'];
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController('error');
        $this->assertAction('error');

        $this->assertQueryContentContains('p', 'no valid movie specified');

        // Can view any movie status (even non-existing movies)
        $urlStatusShort = $url . '/1234567';
        $this->dispatch($urlStatusShort);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('.mqueue_status.mqueue_status_1', 'Need');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_2', 'Bad');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_3', 'Ok');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_4', 'Excellent');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_5', 'Favorite');

        // Can view any movie status with bigger ID (even non-existing movies)
        $urlStatusLong = $url . '/1234567890';
        $this->dispatch($urlStatusLong);

        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);

        $this->assertQueryContentContains('.mqueue_status.mqueue_status_1', 'Need');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_2', 'Bad');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_3', 'Ok');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_4', 'Excellent');
        $this->assertQueryContentContains('.mqueue_status.mqueue_status_5', 'Favorite');
    }
}
