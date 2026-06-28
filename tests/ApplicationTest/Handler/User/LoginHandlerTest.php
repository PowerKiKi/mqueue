<?php

namespace ApplicationTest\Handler\User;

use Application\Model\User;
use ApplicationTest\Handler\AbstractHandler;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;

class LoginHandlerTest extends AbstractHandler
{
    use TestWithTransactionAndUser;

    public function testBasic(): void
    {
        self::assertNull(User::getCurrent(), 'at first we are not logged in');

        $response = $this->handle(new ServerRequest(uri: '/user/login', serverParams: ['HTTP_REFERER' => 'https://mqueue.lan/movie/add']));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('"https&#x3A;&#x2F;&#x2F;mqueue.lan&#x2F;movie&#x2F;add"', $response->getBody());
    }

    public function testLoginAndLogout(): void
    {
        self::assertNull(User::getCurrent(), 'at first we are not logged in');

        $response = $this->handle(new ServerRequest(uri: '/user/login')
            ->withMethod('POST')
            ->withParsedBody([
                'email' => 'user1@example.com',
                'password' => 'user1',
            ]));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(['/movie'], $response->getHeader('location'), 'successful subscription redirect to movie list');
        self::assertNotNull(User::getCurrent(), 'after login, we are login');
        self::assertSame('user1', User::getCurrent()->nickname);

        $response = $this->handle(new ServerRequest(uri: '/user/logout'));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertStringContainsString('You successfully log out', $response->getBody());
        self::assertNull(User::getCurrent(), 'after logged out, we are logged out');
    }

    public function testFailedLogin(): void
    {
        self::assertNull(User::getCurrent(), 'at first we are not logged in');

        $response = $this->handle(new ServerRequest(uri: '/user/login')
            ->withMethod('POST')
            ->withParsedBody([
                'email' => 'user1@example.com',
                'password' => 'invalid',
            ]));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(['/user/login'], $response->getHeader('location'), 'successful subscription redirect to movie list');
        self::assertNull(User::getCurrent(), 'after login, we are login');
    }

    public function testRedirect(): void
    {
        self::assertNull(User::getCurrent(), 'at first we are not logged in');

        $response = $this->handle(new ServerRequest(uri: '/user/login', serverParams: ['HTTP_REFERER' => 'https://mqueue.lan/movie/add'])
            ->withMethod('POST')
            ->withParsedBody([
                'email' => 'user1@example.com',
                'password' => 'user1',
                'referrer' => '/movie/add',
            ]));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(['/movie/add'], $response->getHeader('location'), 'successful subscription redirect to REFERER');
    }
}
