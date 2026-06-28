<?php

declare(strict_types=1);

namespace ApplicationTest\Handler;

use ApplicationTest\Traits\TestWithTransactionAndUser;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;

final class HomePageHandlerTest extends AbstractHandler
{
    use TestWithTransactionAndUser;

    public function testBasic(): void
    {
        $response = $this->handle(new ServerRequest(uri: '/'));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(['/activity'], $response->getHeader('location'));
    }

    public function testWithUser(): void
    {
        $this->setCurrentUser('user1');
        $response = $this->handle(new ServerRequest(uri: '/'));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(['/movie'], $response->getHeader('location'));
    }
}
