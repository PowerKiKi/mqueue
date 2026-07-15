<?php

namespace ApplicationTest\Handler;

use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;

class JsMqueueUserHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/js/mqueue.user.js'));

        self::assertInstanceOf(TextResponse::class, $response);
        self::assertSame(['application/javascript; charset=utf-8'], $response->getHeader('content-type'));
        self::assertStringContainsString('document.evaluate', $response->getBody());
    }
}
