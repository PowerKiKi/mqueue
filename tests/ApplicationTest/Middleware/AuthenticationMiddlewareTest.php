<?php

declare(strict_types=1);

namespace ApplicationTest\Middleware;

use Application\Middleware\AuthenticationMiddleware;
use Application\Model\User;
use Application\Repository\UserRepository;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Session\Session;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddlewareTest extends TestCase
{
    public function testEmptySession(): void
    {
        $session = $this->process(false, null);

        self::assertFalse($session->has('user'));
        self::assertTrue($session->has('other'));
        self::assertNull(User::getCurrent());
    }

    public function testUserNotFound(): void
    {
        $user = null;
        $session = $this->process(true, $user);

        self::assertFalse($session->has('user'));
        self::assertFalse($session->has('other'));
        self::assertNull(User::getCurrent());
    }

    public function testUserNoLimit(): void
    {
        $user = new User();
        $session = $this->process(true, $user);

        self::assertTrue($session->has('user'));
        self::assertTrue($session->has('other'));
        self::assertSame($user, User::getCurrent());
    }

    private function process(bool $userInSession, ?User $user): SessionInterface
    {
        User::setCurrent(null);

        $userRepository = new class($user) extends UserRepository {
            public function __construct(
                private readonly ?User $user,
            ) {}

            public function findOneById(int $id): ?User
            {
                return $this->user;
            }
        };

        $session = new Session(['other' => 'foo']);
        if ($userInSession) {
            $session->set('user', 123);
        }
        $request = new ServerRequest();
        $request = $request->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $response = new Response();
        $handler = new class($response) implements RequestHandlerInterface {
            public function __construct(
                private readonly ResponseInterface $response,
            ) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };

        $middleware = new AuthenticationMiddleware($userRepository);
        $middleware->process($request, $handler);

        return $session;
    }
}
