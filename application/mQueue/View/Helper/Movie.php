<?php

namespace mQueue\View\Helper;

use mQueue\Model\User;
use Zend_View_Helper_Abstract;

class Movie extends Zend_View_Helper_Abstract
{
    /**
     * Returns a string for a movie. It is composed of a link to IMDb and the movie title which links to the movie page.
     *
     * @param \mQueue\Model\Movie $movie
     *
     * @return string
     */
    public function movie(\mQueue\Model\Movie $movie): string
    {
        $result = $this->view->link($movie);

        $user = User::getCurrent();
        if ($user) {
            $status = $movie->getStatus($user);
            $title = $this->view->translate('Your rating is : %s', [$status->getName()]);
        } else {
            $title = $this->view->translate('You are not logged in');
        }

        $movieUrl = $this->view->url(['controller' => 'movie', 'action' => 'view', 'id' => $movie->id], 'singleid', true);
        $result .= ' <a title="' . $title . '" href="' . $movieUrl . '">' . $this->view->escape($movie->getTitle()) . '</a>';

        return $result;
    }
}
