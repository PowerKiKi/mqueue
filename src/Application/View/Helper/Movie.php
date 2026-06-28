<?php

namespace Application\View\Helper;

use Application\Model\User;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasView\UrlHelper;

class Movie
{
    public function __construct(
        private readonly EscapeHtml $escapeHtml,
        private readonly Link $link,
        private readonly UrlHelper $url,
    ) {}

    /**
     * Returns a string for a movie. It is composed of a link to IMDb and the movie title which links to the movie page.
     */
    public function __invoke(\Application\Model\Movie $movie): string
    {
        $result = ($this->link)($movie);

        $user = User::getCurrent();
        if ($user) {
            $status = $movie->getStatus($user);
            $title = _tr('Your rating is : %status%', ['status' => $status->getName()]);
        } else {
            $title = _tr('You are not logged in');
        }

        $movieUrl = ($this->url)('movie.view', ['id' => $movie->id]);
        $result .= ' <a title="' . $title . '" href="' . $movieUrl . '">' . ($this->escapeHtml)($movie->title) . '</a>';

        return $result;
    }
}
