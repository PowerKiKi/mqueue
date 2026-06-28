<?php

namespace ApplicationTest\Handler\User;

use ApplicationTest\Handler\AbstractHandler;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class ViewHandlerTest extends AbstractHandler
{
    use TestWithTransactionAndUser;

    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/user/view/1001'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('<h2>user1</h2>', $response->getBody());
    }

    public function testNotFound(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/user/view/123'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('User not found', $response->getBody());
    }
}
