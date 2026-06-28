<?php

namespace Application\View\Helper;

use Application\Model\Status;
use Laminas\Paginator\Paginator;
use Mezzio\LaminasView\UrlHelper;

class Activity
{
    public function __construct(
        private readonly Gravatar $gravatar,
        private readonly Movie $movie,
        private readonly StatusLinks $statusLinks,
        private readonly UrlHelper $url,
    ) {}

    /**
     * Returns an HTML table of activities.
     *
     * @param Paginator<Status> $activity
     * @param string[] $hiddenColumns optionally hidden columns
     */
    public function __invoke(Paginator $activity, array $hiddenColumns = []): string
    {
        $columns = ['date', 'user', 'status', 'movie'];
        $columns = array_diff($columns, $hiddenColumns);

        $result = '<div class="activity">';

        /** @var Status $status */
        foreach ($activity as $status) {
            $user = $status->user;
            $movie = $status->movie;

            $result .= '<div class="row">';

            if (in_array('user', $columns, true)) {
                $result .= '<div class="user"><a href="' . ($this->url)('user.view', [
                    'id' => $user->id,
                ]) . '">' . ($this->gravatar)($user, 'medium') . '</a></div>';
            }

            $result .= '<div class="others">';

            if (in_array('movie', $columns, true)) {
                $result .= '<div class="movie">' . ($this->movie)($movie) . '</div>';
            }
            if (in_array('status', $columns, true)) {
                $result .= '<span class="rating">' . ($this->statusLinks)($status) . '</span> ';
            }

            if (in_array('date', $columns, true)) {
                $result .= '<time class="dateUpdate timestamp" datetime="' . $status->dateUpdate->toIso8601String() . '" title="' . $status->dateUpdate->toIso8601String() . '">' . $status->dateUpdate->toDateTimeString() . '</time>';
            }

            $result .= '</div>';

            $result .= '</div>';
        }

        if ($activity->count() === 0) {
            $result .= '<p>' . _tr('There is no activity to show.') . '<p>';
        }

        return $result . '</div>';
    }
}
