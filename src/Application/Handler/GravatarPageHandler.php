<?php

namespace Application\Handler;

use Application\Model\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GravatarPageHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [
            'users' => _em()->getRepository(User::class)->getAll(),
            'layout' => false,
        ];

        return new HtmlResponse($this->template->render('app::gravatar-css', $data), headers: ['content-type' => 'text/css']);
    }
}
