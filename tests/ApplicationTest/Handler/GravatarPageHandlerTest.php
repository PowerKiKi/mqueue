<?php

namespace ApplicationTest\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class GravatarPageHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/css/gravatar.css'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertSame(['text/css'], $response->getHeader('content-type'));
        self::assertStringContainsString('span.gravatar', $response->getBody());
        self::assertStringContainsString('span.gravatar.big', $response->getBody());
    }
}
