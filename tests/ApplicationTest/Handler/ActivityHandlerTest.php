<?php

namespace ApplicationTest\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Laminas\Diactoros\ServerRequest;

class ActivityHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/activity'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<h2>Overall activity', $response->getBody());
    }

    public function testUser(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/activity/user/1001'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<h2>Activity for user1', $response->getBody());
    }

    public function testMovie(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/activity/movie/96446'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<h2>Activity for Willow', $response->getBody());
    }

    public function testRss(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/activity', queryParams: ['format' => 'rss']));

        self::assertInstanceOf(XmlResponse::class, $response);
        self::assertStringContainsString('<title>mQueue - Overall activity</title>', $response->getBody());
    }
}
