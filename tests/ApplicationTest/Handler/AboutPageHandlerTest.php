<?php

namespace ApplicationTest\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class AboutPageHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/about'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<p>This project started as a personal need', $response->getBody());
    }
}
