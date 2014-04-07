<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;
use \mQueue\Model\User;

class Movie extends Zend_View_Helper_Abstract
{

    /**
     * Returns a string for a movie. It is composed of a link to IMDb and the movie title which links to the movie page.
     * @param \mQueue\Model\Movie $movie
     */
    public function movie(\mQueue\Model\Movie $movie)
    {
        $result = $this->view->link($movie);

        $user = User::getCurrent();
        if ($user) {
            $status = $movie->getStatus($user);
            $title = $this->view->translate('Your rating is : %s', array($status->getName()));
        } else {
            $title = $this->view->translate('You are not logged in');
        }

        $movieUrl = $this->view->url(array('controller' => 'movie', 'action' => 'view', 'id' => $movie->id), 'singleid', true);
        $result .= ' <a title="' . $title . '" href="' . $movieUrl . '">' . $this->view->escape($movie->getTitle()) . '</a>';

        return $result;
    }

}
