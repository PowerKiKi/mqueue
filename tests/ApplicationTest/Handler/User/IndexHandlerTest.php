<?php

namespace ApplicationTest\Handler\User;

use ApplicationTest\Handler\AbstractHandler;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class IndexHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/user'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<h2>Users list', $response->getBody());
    }
}
