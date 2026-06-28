<?php

declare(strict_types=1);

namespace Application\Handler;

use Application\Model\User;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HomePageHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (User::getCurrent()) {
            return new RedirectResponse('/movie');
        }

        return new RedirectResponse('/activity');

    }
}
