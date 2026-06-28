<?php

namespace ApplicationTest\Handler\User;

use Application\Model\User;
use ApplicationTest\Handler\AbstractHandler;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;

class NewHandlerTest extends AbstractHandler
{
    use TestWithTransactionAndUser;

    public function testBasic(): void
    {
        self::assertNull(User::getCurrent(), 'at first we are not logged in');

        $response = $this->handle(new ServerRequest(uri: '/user/new'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('Password:', $response->getBody());
    }

    public function testNew(): void
    {
        self::assertNull(User::getCurrent(), 'at first we are not logged in');

        $response = $this->handle(new ServerRequest(uri: '/user/new')
            ->withMethod('POST')
            ->withParsedBody([
                'nickname' => 'new test user',
                'email' => 'new_valid@email.org',
                'password' => 'superpassword',
            ]));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(['/movie'], $response->getHeader('location'), 'successful subscription redirect to movie list');
        self::assertNotNull(User::getCurrent(), 'after subscription, we are automatically logged in');
        self::assertSame('new test user', User::getCurrent()->nickname);
    }
}
