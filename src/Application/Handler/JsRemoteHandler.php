<?php

namespace Application\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsRemoteHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        global $container;
        $config = $container->get('config');
        $data = [
            'minimize' => (bool) $config['minimize'],
        ];

        return $this->js('app::remote-js', $data);
    }
}
