<?php

namespace mQueue\Model;

use Zend_Date;
use Zend_Db_Expr;
use Zend_Db_Table_Select;

abstract class MovieMapper extends AbstractMapper
{
    /**
     * Returns a movie by its ID
     *
     * @param int $id
     *
     * @return null|Movie
     */
    public static function find($id)
    {
        $result = self::getDbTable()->find([$id]);

        return $result->current();
    }

    /**
     * Returns all movies
     *
     * @return Movie[]
     */
    public static function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll();

        return $resultSet;
    }

    /**
     * Returns movies for search
     *
     * @return Movie[]
     */
    public static function findAllForSearch()
    {
        $futureYears = [];
        $date = Zend_Date::now();
        for ($i = 0; $i < 10; ++$i) {
            $date->addYear(1);
            $futureYears[] = $date->get(Zend_Date::YEAR_8601);
        }

        $select = self::getDbTable()->select()->setIntegrityCheck(false)
            ->from('movie')
            ->join('status', 'status.idMovie = movie.id AND status.isLatest AND rating = ' . Status::Need, [])
            ->where('source IS NULL')
            ->where('dateSearch IS NULL OR dateSearch < DATE_SUB(NOW(), INTERVAL searchCount MONTH)')// Search for same movie with an incrementally longer interval (1 month, 2 month, 3 month, etc.)
            ->where('dateRelease IS NOT NULL')// Movie must be released ...
            ->where('dateRelease < DATE_SUB(NOW(), INTERVAL 1 MONTH)')// ...at least released one month ago, or longer
            ->group('movie.id')
            ->order('COUNT(movie.id) DESC')// First, order by popularity, so get the most needed first
            ->order('RAND() ASC')// Then, randomize a little bit so we don't always look for the same movies
            ->limit(5);

        $records = self::getDbTable()->fetchAll($select);

        return $records;
    }

    /**
     * Returns movies for data fetching
     *
     * @param int $limit
     *
     * @return Movie[]
     */
    public static function findAllForFetching($limit = null)
    {
        $select = self::getDbTable()->select()->setIntegrityCheck(false)
            ->from('movie')
            ->where('dateUpdate IS NULL OR dateUpdate < DATE_SUB(NOW(), INTERVAL 1 MONTH)')// Don't fetch data for movies, more than once a month
            ->order('RAND()'); // Randomize order, so we don't watch only old movies

        if ($limit) {
            $select->limit(20);
        }

        $records = self::getDbTable()->fetchAll($select);

        return $records;
    }

    /**
     * Returns a query filtered according to parameters. This query may be used with paginator.
     *
     * @param array $filters
     * @param string $orderBy valid SQL sorting snippet
     *
     * @return Zend_Db_Table_Select
     */
    public static function getFilteredQuery(array $filters, string $orderBy)
    {
        $orderBy = preg_replace('/^(status\d+)(.*)/', '\\1.rating\\2', $orderBy);

        $select = self::getDbTable()->select()->setIntegrityCheck(false)
            ->from('movie')
            ->order($orderBy);

        $i = 0;
        $maxDate = '';
        $filtersDone = [];
        foreach ($filters as $key => $filter) {
            if (!is_array($filter)) {
                continue;
            }

            $filterUniqueId = $filter['user'];
            if (!preg_match('/^filter\d+$/', $key) || in_array($filterUniqueId, $filtersDone)) {
                continue;
            }

            $filtersDone[] = $filterUniqueId;
            $showNoRating = in_array(0, $filter['status']);
            $alias = 'status' . $i++;
            $allowNull = ' OR ' . $alias . '.idUser IS NULL';
            if ($filter['condition'] === 'is') {
                $condition = '';
                $allowNull = $showNoRating ? $allowNull : '';
            } else {
                $condition = 'NOT ';
                $allowNull = !$showNoRating ? $allowNull : '';
            }
            $select->joinLeft([$alias => 'status'], '(movie.id = ' . $alias . '.idMovie AND ' . $alias . '.idUser = ' . $filter['user'] . ')' . $allowNull, []);

            $select->where($alias . '.isLatest = 1 OR ' . $alias . '.isLatest IS NULL');

            // Filter by status
            $select->where($alias . '.rating ' . $condition . 'IN (?)' . $allowNull, $filter['status']);

            // Filter by title
            if (isset($filter['title'])) {
                $title = $filter['title'];
                $id = Movie::extractId($title);
                $titles = explode(' ', trim($title));
                foreach ($titles as $part) {
                    if ($part) {
                        $select->where('movie.title LIKE ? OR movie.id = "' . $id . '"', '%' . $part . '%');
                    }
                }
            }

            // Filter by presence of source
            if (isset($filter['withSource']) && $filter['withSource']) {
                $select->where('movie.source IS NOT NULL');
            }

            if ($maxDate) {
                $maxDate = 'IF(`' . $alias . '`.`dateUpdate` IS NULL OR `' . $alias . '`.`dateUpdate` < ' . $maxDate . ', ' . $maxDate . ', `' . $alias . '`.`dateUpdate`)';
            } else {
                $maxDate = '`' . $alias . '`.`dateUpdate`';
            }
        }

        $select->columns(['date' => new Zend_Db_Expr($maxDate)]);

        return $select;
    }

    /**
     * Delete obsolete sources for all movies.
     * An obsolete source is either a source older than 3 months, or
     * a source which is not needed anymore (nobody need the movie anymore)
     */
    public static function deleteObsoleteSources(): void
    {
        $db = self::getDbTable()->getAdapter();
        $update = 'UPDATE `movie` SET dateUpdate = dateUpdate, dateSearch = NULL, searchCount = 0, identity = 0, quality = 0, score = 0, source = NULL';

        // Delete sources older than 6 months
        $db->query($update . ' WHERE `source` IS NOT NULL AND `dateSearch` < DATE_SUB(NOW(), INTERVAL 6 MONTH)');

        // Delete non-needed sources
        $db->query($update . ' WHERE `movie`.`id` NOT IN (SELECT `status`.`idMovie` FROM `status` WHERE `rating` = ? AND `isLatest`)', [Status::Need]);
    }
}
