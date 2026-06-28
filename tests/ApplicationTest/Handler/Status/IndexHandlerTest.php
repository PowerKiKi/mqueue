<?php

namespace ApplicationTest\Handler\Status;

use Application\Response\JsonCallbackResponse;
use ApplicationTest\Handler\AbstractHandler;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;

class IndexHandlerTest extends AbstractHandler
{
    use TestWithTransactionAndUser;

    public function testNoMoviesShouldError(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/status/invalid-id'));

        $body = (string) $response->getBody();
        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('No valid movie specified', $body);
    }

    public function testHtml(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/status/96446'));

        $body = (string) $response->getBody();
        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('Current status', $body);
    }

    public function testNonExistingMovie(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/status/96446', queryParams: ['jsoncallback' => 'myCallback']));

        $body = (string) $response->getBody();
        self::assertInstanceOf(JsonCallbackResponse::class, $response);
        self::assertStringContainsString('myCallback(', $body);
    }

    public function testSetStatus(): void
    {
        $this->setCurrentUser('user1');
        $response = $this->handle(new ServerRequest(uri: '/status/96446/3'));

        $body = (string) $response->getBody();
        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('mqueue_status mqueue_status_3 current', $body);
    }
}
