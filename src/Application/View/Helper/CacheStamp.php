<?php

namespace Application\View\Helper;

class CacheStamp
{
    public function __construct(
        private readonly bool $minimize,
    ) {}

    /**
     * Inject the last modified time of file.
     * This avoids browser cache and force reloading when the file changed.
     */
    public function __invoke(string $fileName): string
    {
        // In development, use non minified version
        if (!$this->minimize) {
            $fileName = str_replace('/js/min/', '/js/', $fileName);
        }

        $fullPath = 'public/' . $fileName;
        if (is_file($fullPath)) {
            $fileName = $fileName . '?' . filemtime($fullPath);
        }

        return $fileName;
    }
}
