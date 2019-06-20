<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_HeadScript;

class HeadScript extends Zend_View_Helper_HeadScript
{
    /**
     * Include a directory recursively
     *
     * @param string $directory
     * @param string $method
     * @param array $args
     */
    private function includeDirectory($directory, $method, array $args): void
    {
        $prefix = APPLICATION_PATH . '/../';
        foreach (array_reverse(glob($prefix . $directory . '/*')) as $file) {
            if (is_dir($file)) {
                $this->includeDirectory($file, $method, $args);
            } else {
                $args[0] = $this->view->cacheStamp(str_replace($prefix . 'public/', '', $file));
                parent::__call($method, $args);
            }
        }
    }

    /**
     * Override parent to support timestamp, compilation and concatenation.
     * Compiled and concatenated files must pre-exist (compiled by external tools).
     *
     * @param string $method
     * @param array $args
     *
     * @return self
     */
    public function __call($method, $args)
    {
        if (mb_strpos($method, 'File')) {
            $fileName = $args[0];

            // If file will be concatenated, use concatenation system instead
            if (is_array($fileName)) {
                // If we are in development, actually don't concatenate anything
                if (APPLICATION_ENV == 'development') {
                    foreach ($fileName[1] as $f) {
                        $this->includeDirectory('public' . $f, $method, $args);
                    }

                    return $this;
                }

                // Otherwise use pre-existing concatenated file
                $fileName = $fileName[0];
            }

            $args[0] = $this->view->cacheStamp($fileName);
        }

        return parent::__call($method, $args);
    }
}
