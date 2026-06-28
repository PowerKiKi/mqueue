<?php

namespace Application\Service;

use Cake\Chronos\Chronos;
use Exception;
use Psr\Log\AbstractLogger;
use Stringable;

final class Logger extends AbstractLogger
{
    /**
     * @var false|resource
     */
    private $file;

    public function __construct()
    {
        $this->file = fopen('logs/all.log', 'a+b');
        if (!$this->file) {
            throw new Exception('Cannot open log file');
        }
    }

    public function __destruct()
    {
        fclose($this->file);
        $this->file = false;
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        if (!$this->file) {
            return;
        }

        foreach ($context as $k => $v) {
            $placeholder = '{' . $k . '}';
            if (!str_contains($message, $placeholder)) {
                continue;
            }

            $message = str_replace($placeholder, $this->serialize($v), $message);
            unset($context[$k]);
        }

        $line = '[' . Chronos::now()->toIso8601String() . '] ' . $level . ': ' . $message . ($context ? ' ' . $this->serialize($context) : '');
        fwrite($this->file, $line . PHP_EOL);
    }

    private function serialize(mixed $v): string
    {
        if (is_string($v)) {
            return $v;
        }

        return json_encode($v);
    }
}
