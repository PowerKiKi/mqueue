<?php

namespace Application\View\Helper;

class HeadScript extends \Laminas\View\Helper\HeadScript
{
    public function __construct(
        private readonly bool $minimize,
        private readonly CacheStamp $cacheStamp,
    )
    {
        parent::__construct();
    }

    /**
     * Include a directory recursively.
     */
    private function includeDirectory(string $directory): void
    {
        foreach (array_reverse(glob($directory . '/*', GLOB_ONLYDIR)) as $file) {
            if (is_dir($file)) {
                $this->includeDirectory($file);
            } else {
                $file = ($this->cacheStamp)(str_replace('public/', '', $file));
                $this->prependFile($file);
            }
        }
    }

    /**
     * Support timestamp, compilation and concatenation of folder.
     * Compiled and concatenated files must pre-exist (compiled by external tools).
     */
    public function prependDirectory(string $fileName, string $directory): self
    {
        if ($this->minimize) {
            $this->prependFile(($this->cacheStamp)($fileName));
        } else {
            $this->includeDirectory('public' . $directory);
        }

        return $this;
    }
}
