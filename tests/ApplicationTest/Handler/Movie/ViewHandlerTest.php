<?php

namespace ApplicationTest\Handler\Movie;

use ApplicationTest\Handler\AbstractHandler;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class ViewHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie/view/96446'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<h2>Willow</h2>', $response->getBody());
    }

    public function testNotFound(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/movie/view/123'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('Movie not found', $response->getBody());
    }
}
