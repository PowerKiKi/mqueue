<?php

namespace ApplicationTest\Handler;

use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;

class JsRemoteHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/js/remote.js'));

        self::assertInstanceOf(TextResponse::class, $response);
        self::assertSame(['application/javascript; charset=utf-8'], $response->getHeader('content-type'));
        self::assertStringContainsString('jquery', $response->getBody());
        self::assertStringContainsString('mqueue', $response->getBody());
    }
}
