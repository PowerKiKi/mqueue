<?php

abstract class Default_Model_MovieMapper extends Default_Model_AbstractMapper
{
	/**
	 * Returns a movie by its ID
	 * @param integer $id
	 * @return Default_Model_Movie|null
	 */
    public static function find($id)
    {
        $result = self::getDbTable()->find($id);

        return $result->current();
    }

    /**
     * Returns all movies
     * @return Default_Model_Movie[]
     */
    public static function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll();

        return $resultSet;
    }

	/**
	 * Returns movies for search
	 * @return Default_Model_Movie[]
	 */
	public static function findAllForSearch()
	{
		$futureYears = array();
		$date = Zend_Date::now();
		for ($i = 0; $i < 10; $i++)
		{
			$date->addYear(1);
			$futureYears[] = $date->get(Zend_Date::YEAR_8601);
		}

		$select = self::getDbTable()->select()->setIntegrityCheck(false)
			->from('movie')
			->join('status', 'status.idMovie = movie.id AND status.isLatest AND rating = ' . Default_Model_Status::Need , array())
			->where('source IS NULL')
			->where('dateSearch IS NULL OR dateSearch < DATE_SUB(NOW(), INTERVAL 1 MONTH)') // Don't search for same movie more than once a month
			->where('dateRelease IS NOT NULL') // Movie must be released ...
			->where('dateRelease < DATE_SUB(NOW(), INTERVAL 1 MONTH)') // ...at least released one month ago, or longer
			->group('movie.id')
			->order('RAND()') // Randomize order, so we don't watch only old movies
			->limit(5)
			;
		
		$records = self::getDbTable()->fetchAll($select);

		return $records;
	}

	/**
	 * Returns movies for data fetching
	 * @return Default_Model_Movie[]
	 */
	public static function findAllForFetching($limit = null)
	{
		$select = self::getDbTable()->select()->setIntegrityCheck(false)
			->from('movie')
			->where('dateUpdate IS NULL OR dateUpdate < DATE_SUB(NOW(), INTERVAL 1 MONTH)') // Don't fetch data for movies, more than once a month
			->order('RAND()') // Randomize order, so we don't watch only old movies
			;

        if ($limit)
            $select->limit(20);

		$records = self::getDbTable()->fetchAll($select);

		return $records;
	}

    /**
     * Returns a query filtered according to parameters. This query may be used with paginator.
     * @param array $filters
     * @param string $orderBy valid SQL sorting snippet
     * @return Zend_Db_Table_Select
     */
    public static function getFilteredQuery(array $filters, $orderBy)
    {

        $orderBy = preg_replace('/^(status\d+)(.*)/', '\\1.rating\\2', $orderBy);

		$select = self::getDbTable()->select()->setIntegrityCheck(false)
			->from('movie')
			->order($orderBy);

		$i = 0;
		$maxDate = '';
		$filtersDone = array();
		foreach ($filters as $key => $filter)
		{
			$filterUniqueId = $filter['user'] . $filter['status'];
			if (!preg_match('/^filter\d+$/', $key) || in_array($filterUniqueId, $filtersDone))
				continue;

			$filtersDone []= $filterUniqueId;

			$alias = 'status' . $i++;
			$allowNull = ($filter['status'] == 0 || $filter['status'] == -2 ? ' OR ' . $alias . '.idUser IS NULL' : '');
			$select->joinLeft(array($alias => 'status'), '(movie.id = ' . $alias . '.idMovie AND ' . $alias . '.idUser = ' . $filter['user'] . ')' . $allowNull, array());

			$select->where($alias . '.isLatest = 1 OR ' . $alias . '.isLatest IS NULL');

			// Filter by status, not rated or a specific rating
			if ($filter['status'] >= 0 && $filter['status'] <= 5)
			{
				$select->where($alias . '.rating = ?' . $allowNull, $filter['status']);
			}
			// All rated
			elseif ($filter['status'] == -1)
			{
				$select->where($alias . '.rating <> ?' . $allowNull, 0);
			}

			// Filter by title
			if (isset($filter['title']))
			{
				$titles = explode(' ', trim($filter['title']));
		    	foreach ($titles as $part)
		    	{
		    		if ($part)
		    			$select->where('movie.title LIKE ?', '%' . $part . '%');
		    	}
			}

			// Filter by presence of source
			if (isset($filter['withSource']) && $filter['withSource'])
			{
		    	$select->where('movie.source IS NOT NULL');
			}

			if ($maxDate)
				$maxDate = 'IF(`' . $alias . '`.`dateUpdate` IS NULL OR `' . $alias . '`.`dateUpdate` < ' . $maxDate . ', ' . $maxDate . ', `' . $alias . '`.`dateUpdate`)';
			else
				$maxDate = '`' . $alias . '`.`dateUpdate`';
		}

		$select->columns(array('date' => new Zend_Db_Expr($maxDate)));

    	return $select;
    }

	/**
	 * Delete obsolete sources for all movies.
	 * An obsolete source is either a source older than 3 months, or
	 * a source which is not needed anymore (nobody need the movie anymore)
	 */
	public static function deleteObsoleteSources()
	{
		$db = self::getDbTable()->getAdapter();
		$update = 'UPDATE `movie` SET dateUpdate = dateUpdate, dateSearch = NULL, searchCount = 0, identity = 0, quality = 0, score = 0, source = NULL';

		// Delete sources older than 6 months
		$db->query($update . ' WHERE `source` IS NOT NULL AND `dateSearch` < DATE_SUB(NOW(), INTERVAL 6 MONTH)');

		// Delete non-needed sources
		$db->query($update . ' WHERE `movie`.`id` NOT IN (SELECT `status`.`idMovie` FROM `status` WHERE `rating` = ? AND `isLatest`)', array(Default_Model_Status::Need));
	}
}
