<?php

namespace mQueue\View\Helper;

use mQueue\Model\MovieMapper;
use mQueue\Model\UserMapper;
use Zend_Date;
use Zend_Paginator;
use Zend_View_Helper_Abstract;

class Activity extends Zend_View_Helper_Abstract
{
    /**
     * Returns an HTML table of activities
     *
     * @param Zend_Paginator $activity
     * @param array $hiddenColumns optionally hidden columns
     *
     * @return string
     */
    public function activity(Zend_Paginator $activity, $hiddenColumns = [])
    {
        $columns = ['date', 'user', 'status', 'movie'];
        $columns = array_diff($columns, $hiddenColumns);

        $result = '<div class="activity">';

        $cacheUser = [];
        $cacheMovie = [];
        foreach ($activity as $status) {
            if (!array_key_exists($status->idUser, $cacheUser)) {
                $cacheUser[$status->idUser] = UserMapper::find($status->idUser);
            }
            $user = $cacheUser[$status->idUser];

            if (!array_key_exists($status->idMovie, $cacheMovie)) {
                $cacheMovie[$status->idMovie] = MovieMapper::find($status->idMovie);
            }
            $movie = $cacheMovie[$status->idMovie];

            $result .= '<div class="row">';

            if (in_array('user', $columns)) {
                $result .= '<div class="user"><a href="' . $this->view->url([
                        'controller' => 'user',
                        'action' => 'view',
                        'id' => $user->id,
                    ], 'singleid', true) . '">' . $this->view->gravatar($user, 'medium') . ' ' /* . $this->view->escape($user->nickname) */ . '</a></div>';
            }

            $result .= '<div class="others">';

            if (in_array('movie', $columns)) {
                $result .= '<div class="movie">' . $this->view->movie($movie) . '</div>';
            }
            if (in_array('status', $columns)) {
                $result .= '<span class="rating">' . $this->view->statusLinks($status) . '</span> ';
            }

            if (in_array('date', $columns)) {
                $result .= '<time class="dateUpdate timestamp" datetime="' . $status->getDateUpdate()->get(Zend_Date::ISO_8601) . '" title="' . $status->getDateUpdate()->get(Zend_Date::ISO_8601) . '">' . $status->dateUpdate . '</time>';
            }

            $result .= '</div>';

            $result .= '</div>';
        }

        if ($activity->getTotalItemCount() == 0) {
            $result .= '<p>' . $this->view->translate('There is no activity to show.') . '<p>';
        }

        return $result . '</div>';
    }
}
