<?php

declare(strict_types=1);

namespace Application\Response;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

class JsonCallbackResponse extends Response
{
    public function __construct(
        string $jsonCallback,
        array $data,
        int $status = 200,
    )
    {
        $body = $jsonCallback . '('
            . json_encode([
                'jsonCallback' => $jsonCallback,
                ...$data,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
            . ')';

        $body = $this->createBody($body);

        parent::__construct($body, $status, ['content-type' => 'application/javascript; charset=utf-8']);
    }

    private function createBody(string $json): Stream
    {
        $body = new Stream('php://temp', 'wb+');
        $body->write($json);
        $body->rewind();

        return $body;
    }
}
