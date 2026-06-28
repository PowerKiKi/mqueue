<?php

namespace Application\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsMqueueUserHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->js('app::mqueue-user-js');
    }
}
