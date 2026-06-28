<?php

namespace Application\Handler\User;

use Application\Handler\PageHandler;
use Application\Model\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogoutHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Mezzio\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->clear();

        User::setCurrent(null);

        return new HtmlResponse($this->template->render('app::user/logout'));
    }
}
