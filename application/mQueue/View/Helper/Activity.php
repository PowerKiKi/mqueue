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
     * @param Zend_Paginator $activity
     * @param array $hiddenColumns optionnally hidden columns
     * @return string
     */
    public function activity(Zend_Paginator $activity, $hiddenColumns = [])
    {
        $columns = ['date', 'user', 'status', 'movie'];
        $columns = array_diff($columns, $hiddenColumns);

        $result = '<table class="activity">';

        $result .= '<tr>';
        if (in_array('date', $columns))
            $result .= '<th>' . $this->view->translate('Date') . '</th>';
        if (in_array('user', $columns))
            $result .= '<th>' . $this->view->translate('User') . '</th>';
        if (in_array('status', $columns))
            $result .= '<th>' . $this->view->translate('Rating') . '</th>';
        if (in_array('movie', $columns))
            $result .= '<th>' . $this->view->translate('Movie') . '</th>';
        $result .= '</tr>';

        $cacheUser = [];
        $cacheMovie = [];
        foreach ($activity as $status) {
            if (!array_key_exists($status->idUser, $cacheUser)) {
                $cacheUser[$status->idUser] = \mQueue\Model\UserMapper::find($status->idUser);
            }
            $user = $cacheUser[$status->idUser];

            if (!array_key_exists($status->idMovie, $cacheMovie)) {
                $cacheMovie[$status->idMovie] = \mQueue\Model\MovieMapper::find($status->idMovie);
            }
            $movie = $cacheMovie[$status->idMovie];

            $result .= '<tr>';
            if (in_array('date', $columns))
                $result .= '<td class="dateUpdate timestamp" title="' . $status->getDateUpdate()->get(Zend_Date::ISO_8601) . '">' . $status->dateUpdate . '</td>';
            if (in_array('user', $columns))
                $result .= '<td class="user"><a href="' . $this->view->url([
                            'controller' => 'user',
                            'action' => 'view',
                            'id' => $user->id,
                                ], 'singleid', true) . '">' . $this->view->gravatar($user) . ' ' . $this->view->escape($user->nickname) . '</a></td>';
            if (in_array('status', $columns))
                $result .= '<td class="rating">' . $this->view->statusLinks($status) . '</td>';
            if (in_array('movie', $columns))
                $result .= '<td class="movie">' . $this->view->movie($movie) . '</td>';

            $result .= '</tr>';
        }

        if ($activity->getTotalItemCount() == 0) {
            $result .= '<tr><td colspan="' . count($columns) . '">' . $this->view->translate('There is no activity to show.') . '</td></tr>';
        }

        return $result . '</table>';
    }

}
