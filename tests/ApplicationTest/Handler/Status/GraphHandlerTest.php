<?php

namespace ApplicationTest\Handler\Status;

use ApplicationTest\Handler\AbstractHandler;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

class GraphHandlerTest extends AbstractHandler
{
    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/status/graph'));
        self::assertInstanceOf(JsonResponse::class, $response);

        $body = (string) $response->getBody();
        $chart = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('chart', $chart);
    }

    public function testPercent(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/status/graph', queryParams: ['percent' => true]));
        self::assertInstanceOf(JsonResponse::class, $response);

        $body = (string) $response->getBody();
        $chart = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('chart', $chart);
        self::assertStringContainsString(' [%]', $chart['yAxis']['title']['text']);
    }
}
