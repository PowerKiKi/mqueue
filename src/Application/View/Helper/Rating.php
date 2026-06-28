<?php

namespace Application\View\Helper;

class Rating
{
    /**
     * Returns some help text about statuses for end-user.
     */
    public function __invoke(\Application\Enum\Rating $rating): string
    {
        $result = '<span class="mqueue_status current mqueue_status_' . $rating->value . '" title="' . $rating->name() . '"><span>' . $rating->name() . '</span></span>';

        return $result;
    }
}
