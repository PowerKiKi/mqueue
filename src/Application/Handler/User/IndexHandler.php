<?php

namespace Application\Handler\User;

use Application\Handler\PageHandler;
use Application\Model\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [
            'users' => _em()->getRepository(User::class)->getAll(),
        ];

        return new HtmlResponse($this->template->render('app::user/index', $data));
    }
}
