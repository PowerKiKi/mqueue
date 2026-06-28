<?php

namespace Application\View\Helper;

use Application\Model\Status;
use Application\Model\User;
use Laminas\View\Helper\ServerUrl;
use Mezzio\LaminasView\UrlHelper;

class StatusLinks
{
    public function __construct(
        private readonly ServerUrl $serverUrl,
        private readonly UrlHelper $url,
    ) {}

    /**
     * Returns the set of links to display a status (the icons used to rate movies).
     */
    public function __invoke(Status $status): string
    {
        $result = '<div class="mqueue_status_links mqueue_status_links_' . $status->getUniqueId() . '">';
        $user = User::getCurrent();

        // Deactivate links if no logged user
        if ($user) {
            $tag = 'a';
        } else {
            $tag = 'span';
        }

        foreach (\Application\Enum\Rating::possibleChoices() as $rating) {
            $class = $rating->value . ($status->rating === $rating ? ' current' : '');
            $url = ($this->serverUrl)() . ($this->url)('status.rating', [
                'movie' => $status->movie->id,
                'rating' => ($rating === $status->rating && $user && $user === $status->user) ? 0 : $rating->value,
            ]);

            $result .= '<' . $tag . ' class="mqueue_status mqueue_status_' . $class . '"' . ($tag === 'a' ? ' href="' . $url . '"' : '') . ' title="' . $rating->name() . '"><span>' . $rating->name() . '</span></' . $tag . '>';
        }
        $result .= '<span class="preloader"></span></div>';

        return $result;
    }
}
