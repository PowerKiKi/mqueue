<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;

class Link extends Zend_View_Helper_Abstract
{
    /**
     * Create a link to IMDb page for specified movie.
     *
     * @param \mQueue\Model\Movie $movie
     * @param bool $showUrl
     *
     * @return string
     */
    public function link(\mQueue\Model\Movie $movie, $showUrl = false): string
    {
        $url = $movie->getImdbUrl();
        $result = '<a class="imdb' . ($showUrl ? '' : ' hideUrl') . '" title="' . $url . '" href="' . $url . '"><span>&nbsp;' . $url . '</span></a>';

        return $result;
    }
}
