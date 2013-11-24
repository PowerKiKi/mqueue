<?php

abstract class Default_Model_StatusMapper extends Default_Model_AbstractMapper
{

    /**
     * Define the status for a movie-user tuple. If an existing satus exists and
     * is very recent, it will be updated, otherwise a new status will be created.
     * IMPORTANT: This is the only allowed way to modify status.
     * @param Default_Model_Movie $movie
     * @param Default_Model_User $user
     * @param integer $rating @see Default_Model_Status
     * @return Default_Model_Status
     */
    public static function set(Default_Model_Movie $movie, Default_Model_User $user, $rating)
    {
        $db = self::getDbTable()->getAdapter();
        $db->beginTransaction();

        // Find out if a very recent status exist to be replaced, so user can change their mind "quickly"
        $select = self::getDbTable()->select()
                ->where('idUser = ?', $user->id)
                ->where('idMovie = ?', $movie->id)
                ->where('dateUpdate > DATE_SUB(NOW(), INTERVAL 5 MINUTE)');

        $status = self::getDbTable()->fetchRow($select);

        // Otherwise create a brand new one and set all existing one as "old"
        if (!$status) {
            $status = self::getDbTable()->createRow();
            $status->idUser = $user->id;
            $status->idMovie = $movie->id;
            $status->isLatest = true;

            // Here we must set dateUpdate to itself to avoid auto-update of the timestamp field by MySql
            $db->query('UPDATE `status` SET isLatest = 0, dateUpdate = dateUpdate WHERE idUser = ? AND idMovie = ?', array($user->id, $movie->id));
        }

        $status->rating = $rating;
        $status->save();

        $db->commit();

        return $status;
    }

    /**
     * Find a status by its user and movie. If not found it will be created (but not saved).
     * @param integer $idMovie
     * @param Default_Model_User|null $user
     * @return Default_Model_Status
     */
    public static function find($idMovie, Default_Model_User $user = null)
    {
        $statuses = self::findAll(array($idMovie), $user);

        return reset($statuses);
    }

    /**
     * Returns an array of Status containing all statuses for specified ids
     * (if they don't exist in database, they will be created with default values but not saved)
     *
     * @param array $idMovies
     * @param Default_Model_User|null $user
     * @return array of Default_Model_Status
     */
    public static function findAll(array $idMovies, Default_Model_User $user = null)
    {
        $statuses = array();
        if (!count($idMovies))
            return $statuses;

        // Do not hit database if we know there won't be any result anyway
        if ($user) {
            $select = self::getDbTable()->select()
                    ->where('idUser = ?', $user->id)
                    ->where('idMovie IN (?)', $idMovies)
                    ->where('isLatest = 1');

            $records = self::getDbTable()->fetchAll($select);

            foreach ($records as $record) {
                $statuses[$record->idMovie] = $record;
            }
        }

        // Fill non existing statuses in databases
        foreach ($idMovies as $id) {
            if (!array_key_exists($id, $statuses)) {
                $status = self::getDbTable()->createRow();
                if ($user)
                    $status->idUser = $user->id;
                $status->idMovie = $id;
                $statuses[$status->idMovie] = $status;
            }
        }

        return $statuses;
    }

    /**
     * Build statistic for the given user.
     * @param Default_Model_User $user
     * @return array statistics
     */
    public static function getStatistics(Default_Model_User $user)
    {
        $select = self::getDbTable()->select()->setIntegrityCheck(false)
                ->from('status', array(
                    'rating' => 'IFNULL(rating, 0)',
                    'count' => 'COUNT(IFNULL(rating, 0))'))
                ->joinRight('movie', 'movie.id = status.idMovie AND status.idUser = ' . $user->id, array())
                ->where('isLatest = 1 OR isLatest IS NULL')
                ->group('IFNULL(rating, 0)')
        ;

        $records = self::getDbTable()->fetchAll($select);

        // Set all count to 0
        $result = array('total' => 0, 'rated' => 0, Default_Model_Status::Nothing => 0);
        foreach (Default_Model_Status::$ratings as $val => $name) {
            $result[$val] = 0;
        }

        // Fetch real counts
        foreach ($records->toArray() as $row) {
            $result[$row['rating']] = $row['count'];
            if ($row['rating'] != Default_Model_Status::Nothing) {
                $result['rated'] += $row['count'];
            }
            $result['total'] += $row['count'];
        }

        return $result;
    }

    /**
     * Build statistic for the given user.
     * @param Default_Model_User $user
     * @return array statistics
     */
    public static function getGraph(Default_Model_User $user = null, $percent = false)
    {
        $select = self::getDbTable()->select()
                ->order('dateUpdate')
        ;

        if ($user) {
            $select->where('idUser = ?', $user->id);
        }

        $records = self::getDbTable()->fetchAll($select);

        // Set all count to 0
        $cumulatedStatuses = array(Default_Model_Status::Nothing => 0);
        $graphData = array();
        foreach (Default_Model_Status::$ratings as $val => $name) {
            $cumulatedStatuses[$val] = 0;
            $graphData[$val] = array();
        }

        // Fetch real counts
        $lastStatuses = array();
        foreach ($records as $row) {

            // Add new status
            $cumulatedStatuses[$row->rating] ++;
            $changed = array($row->rating);

            // Substract old status
            if (isset($lastStatuses[$row->idUser][$row->idMovie])) {
                $cumulatedStatuses[$lastStatuses[$row->idUser][$row->idMovie]] --;
                $changed [] = $lastStatuses[$row->idUser][$row->idMovie];
            }
            $lastStatuses[$row->idUser][$row->idMovie] = $row->rating;

            $time = new DateTime($row->dateUpdate);
            $time->setTimezone(new DateTimeZone('GMT'));
            $epoch = (int) $time->format('U') * 1000;

            // If we are in percent mode, we need all status for each timestamp
            if ($percent) {
                $changed = array_keys(Default_Model_Status::$ratings);
            }

            // Keep for the graph only the changed values (and overwrite previous value if it happened at exactly the same time)
            foreach ($changed as $val) {
                $graphData[$val][$epoch] = array(
                    $epoch,
                    $cumulatedStatuses[$val],
                );
            }
        }

        // Format everything in a more output friendly way
        $result = array();
        foreach (Default_Model_Status::$ratings as $val => $name) {
            $result[] = array(
                'name' => $name,
                'data' => array_values($graphData[$val]),
            );
        }

        return $result;
    }

    /**
     * Returns the query to get activity for either the whole system, or a specific user, or a specific movie
     * @param Default_Model_User|Default_Model_Movie|null $item
     * @return Zend_Db_Table_Select
     */
    public static function getActivityQuery($item = null)
    {
        $select = self::getDbTable()->select()
                ->from('status')
                ->order('dateUpdate DESC');

        if ($item instanceof Default_Model_User) {
            $select->where('idUser = ?', $item->id);
        } elseif ($item instanceof Default_Model_Movie) {
            $select->where('idMovie = ?', $item->id);
        }

        return $select;
    }

}
