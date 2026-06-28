<?php

namespace Application\View\Helper;

class HeadLink extends \Laminas\View\Helper\HeadLink
{
    public function __construct(
        private readonly CacheStamp $cacheStamp,
    ) {}

    /**
     * Override parent to inject the last modified time of file.
     * This avoids browser cache and force reloading when the file changed.
     *
     * @param mixed $method
     * @param mixed $args
     */
    public function __call($method, $args)
    {
        if (mb_strpos($method, 'Stylesheet')) {
            $args[0] = ($this->cacheStamp)($args[0]);
        }

        return parent::__call($method, $args);
    }
}
