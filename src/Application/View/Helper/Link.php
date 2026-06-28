<?php

namespace Application\View\Helper;

class Link
{
    /**
     * Create a link to IMDb page for specified movie.
     */
    public function __invoke(\Application\Model\Movie $movie, bool $showUrl = false): string
    {
        $url = $movie->getImdbUrl();
        $result = '<a class="imdb' . ($showUrl ? '' : ' hideUrl') . '" title="' . $url . '" href="' . $url . '"><span>&nbsp;' . $url . '</span></a>';

        return $result;
    }
}
