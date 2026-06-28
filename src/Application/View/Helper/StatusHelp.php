<?php

namespace Application\View\Helper;

class StatusHelp
{
    public function __construct(
        private readonly Rating $rating,
    ) {}

    /**
     * Returns some help text about statuses for end-user.
     */
    public function __invoke(): string
    {
        $result = '<ul class="statusHelp">';
        $result .= '<li>' . ($this->rating)(\Application\Enum\Rating::Need) . ': ' . _tr('I want to see this movie') . '</li>';
        $result .= '<li>' . ($this->rating)(\Application\Enum\Rating::Bad) . ': ' . _tr('Boring movie, I wasted my time') . '</li>';
        $result .= '<li>' . ($this->rating)(\Application\Enum\Rating::Ok) . ': ' . _tr('Enjoyable movie (most movies)') . '</li>';
        $result .= '<li>' . ($this->rating)(\Application\Enum\Rating::Excellent) . ': ' . _tr('Excellent, I would watch it twice') . '</li>';
        $result .= '<li>' . ($this->rating)(\Application\Enum\Rating::Favorite) . ': ' . _tr('Incredibly awesome, the kind of movie you must watch many times regularly') . '</li>';
        $result .= '</ul>';

        return $result;
    }
}
