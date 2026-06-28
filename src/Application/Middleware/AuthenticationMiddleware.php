<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Model\User;
use Application\Repository\UserRepository;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * Load current user from session if exists and still valid.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if ($session->has('user')) {
            $user = $this->userRepository->findOneById($session->get('user'));

            if ($user) {
                User::setCurrent($user);
            }

            // If we were supposed to be logged in, but the user is not available anymore, that means the user
            // was forcibly logged out (likely deleted), so we clear his entire session
            if (!User::getCurrent()) {
                $session->clear();
            }
        }

        return $handler->handle($request);
    }
}
