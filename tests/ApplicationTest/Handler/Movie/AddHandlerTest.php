<?php

namespace ApplicationTest\Handler\Movie;

use ApplicationTest\Handler\AbstractHandler;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class AddHandlerTest extends AbstractHandler
{
    public function testAdd(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie/add'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('IMDb url or id', $response->getBody());
        self::assertStringContainsString('learn how to add', $response->getBody());
    }

    public function testAddSubmitted(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie/add', queryParams: ['id' => 'https://www.imdb.com/title/tt0096446/']));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('Willow', $response->getBody());
    }
}
