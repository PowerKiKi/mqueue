<?php

declare(strict_types=1);

namespace ApplicationTest\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AbstractHandler extends TestCase
{
    protected function tearDown(): void
    {
        _em()->clear();

        parent::tearDown();
    }

    protected function handle(ServerRequestInterface $request): ResponseInterface
    {
        // @var \Mezzio\Application $app
        global $app;

        return $app->handle($request);
    }
}
