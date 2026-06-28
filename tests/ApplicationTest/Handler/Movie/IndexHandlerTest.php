<?php

namespace ApplicationTest\Handler\Movie;

use ApplicationTest\Handler\AbstractHandler;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Laminas\Diactoros\ServerRequest;

class IndexHandlerTest extends AbstractHandler
{
    use TestWithTransactionAndUser;

    public function testIndex(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<legend>Filter</legend>', $response->getBody());
        self::assertStringContainsString('Title', $response->getBody());
    }

    public function testIndexFiltered(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie', queryParams: [
            'filter1' => [
                'user' => 1001,
                'condition' => 'is',
                'status' => [0, 1, 2, 3, 4, 5],
                'title' => '',
                'withSource' => false,
            ],
        ]));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<legend>Filter</legend>', $response->getBody());
        self::assertStringContainsString('Title', $response->getBody());
    }

    public function testIndexSearch(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie', queryParams: ['search' => 'will']));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('Willow', $response->getBody());
    }

    public function testIndexRss(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie', queryParams: ['format' => 'rss']));

        self::assertInstanceOf(XmlResponse::class, $response);
        self::assertStringContainsString('<title>mQueue - user1:Need+Bad+Ok+Excellent+Favorite</title>', $response->getBody());
    }

    public function testNoUsers(): void
    {
        _em()->getConnection()->delete('user');
        $response = $this->handle(new ServerRequest(uri: '/movie'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('At least one user must exist to access this page', $response->getBody());
    }
}
