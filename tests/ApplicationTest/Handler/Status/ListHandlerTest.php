<?php

namespace ApplicationTest\Handler\Status;

use Application\Response\JsonCallbackResponse;
use ApplicationTest\Handler\AbstractHandler;
use Laminas\Diactoros\ServerRequest;

class ListHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        // Can view any movie status (even non-existing movies)
        $response = $this->handle(new ServerRequest(uri: '/status/list/movies/1234567'));
        self::assertInstanceOf(JsonCallbackResponse::class, $response);

        $body = (string) $response->getBody();
        self::assertStringContainsString('<span>Need</span>', $body);
        self::assertStringContainsString('<span>Bad</span>', $body);
        self::assertStringContainsString('<span>Ok</span>', $body);
        self::assertStringContainsString('<span>Excellent</span>', $body);
        self::assertStringContainsString('<span>Favorite</span>', $body);
    }

    public function testBasicBiggerID(): void
    {
        // Can view any movie status with bigger ID (even non-existing movies)
        $response = $this->handle(new ServerRequest(uri: '/status/list/movies/1234567890'));
        self::assertInstanceOf(JsonCallbackResponse::class, $response);

        $body = (string) $response->getBody();
        self::assertStringContainsString('<span>Need</span>', $body);
        self::assertStringContainsString('<span>Bad</span>', $body);
        self::assertStringContainsString('<span>Ok</span>', $body);
        self::assertStringContainsString('<span>Excellent</span>', $body);
        self::assertStringContainsString('<span>Favorite</span>', $body);
    }
}
