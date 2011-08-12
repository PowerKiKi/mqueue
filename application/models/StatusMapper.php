<?php

abstract class Default_Model_StatusMapper extends Default_Model_AbstractMapper
{
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
		if ($user)
		{
			$select = self::getDbTable()->select()
					->where('idUser = ?', $user->id)
					->where('idMovie IN (?)', $idMovies);

			$records = self::getDbTable()->fetchAll($select);

			foreach($records as $record)
			{
				$statuses[$record->idMovie] = $record;
			}
		}

    	// Fill non existing statuses in databases
    	foreach ($idMovies as $id)
    	{
    		if (!array_key_exists($id, $statuses))
    		{
				$status = self::getDbTable()->createRow();
				if ($user) $status->idUser = $user->id;
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
			->from('status',
				array(
				'rating' => 'IFNULL(rating, 0 )',
				'count' => 'count(IFNULL(rating, 0))'))
			->joinRight('movie',
				'movie.id = status.idMovie AND status.idUser = ' . $user->id,
				array())
			->group('IFNULL(rating, 0)')
			;

		$records = self::getDbTable()->fetchAll($select);

		// Set all count to 0
		$result = array('total' => 0, 'rated' => 0, Default_Model_Status::Nothing => 0);
		foreach (Default_Model_Status::$ratings as $val => $name)
		{
			$result[$val] = 0;
		}

		// Fetch real counts
		foreach ($records->toArray() as $row)
		{
			$result[$row['rating']] = $row['count'];
			if ($row['rating'] != Default_Model_Status::Nothing)
			{
				$result['rated'] += $row['count'];
			}
			$result['total'] += $row['count'];
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

		if ($item instanceof Default_Model_User)
		{
			$select->where('idUser = ?', $item->id);
		}
		elseif ($item instanceof Default_Model_Movie)
		{
			$select->where('idMovie = ?', $item->id);
		}

		return $select;
	}
}
