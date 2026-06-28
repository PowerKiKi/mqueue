<?php

namespace ApplicationTest\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class FaqPageHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/faq'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('Create an account', $response->getBody());
        self::assertStringContainsString('I want to see this movie', $response->getBody());
    }
}
