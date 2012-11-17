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
			->join('status', 'status.idMovie = movie.id AND rating = ' . Default_Model_Status::Need , array())
			->where('source IS NULL')
			->where('dateSearch IS NULL OR dateSearch < DATE_SUB(NOW(), INTERVAL 1 MONTH)') // Don't search for same movie more than once a month
			->where('title NOT REGEXP ?', '\(.*(' . join('|', $futureYears) . ').*\)') // Avoid movies not yet released
			->group('movie.id')
			->order('RAND()') // Randomize order, so we don't watch only old movies
			->limit(5)
			;
		
		$records = self::getDbTable()->fetchAll($select);
		
		return $records;
	}

    /**
     * Returns a query filtered according to parameters. This query may be used with paginator.
     * @param array $filters
     * @param string $sort defines the column to sort with
     * @param string $sortOrder defines the order of the sort ("desc" or "asc")
     * @return Zend_Db_Table_Select
     */
    public static function getFilteredQuery(array $filters, $sort, $sortOrder)
    {
    	// Find out what to order (only allowed 'title', 'date', 'status0', 'status1', 'status2', ...)
    	if (preg_match('/^status\d+$/', $sort))
    	{
			$sort = $sort . '.rating';
    	}
    	elseif ($sort != 'date' && $sort != 'dateSearch')
    	{
    		$sort = 'title';
    	}

    	if ($sortOrder == 'desc')
    		$sortOrder = 'DESC';
    	else
    		$sortOrder = 'ASC';


		$select = self::getDbTable()->select()->setIntegrityCheck(false)
			->from('movie')
			->order($sort . ' ' . $sortOrder);

		$i = 0;
		$maxDate = '';
		$filtersDone = array();
		foreach ($filters as $key => $filter)
		{
			$filterUniqueId = $filter['user'] . $filter['status'];
			if (!preg_match('/^filter\d+$/', $key) || in_array($filterUniqueId, $filtersDone))
				continue;

			$filtersDone []= $filterUniqueId;

			$allowNull = ($filter['status'] == 0 || $filter['status'] == -2 ? ' OR status' . $i . '.idUser IS NULL' : '');
			$select->joinLeft(array('status' . $i => 'status'), '(movie.id = status' . $i . '.idMovie AND status' . $i . '.idUser = ' . $filter['user'] . ')' . $allowNull, array());

			// Filter by status, not rated or a specific rating
			if ($filter['status'] >= 0 && $filter['status'] <= 5)
			{
				$select->where('(status' . $i . '.isLatest = 1 AND status' . $i . '.rating = ?)' . $allowNull, $filter['status']);
			}
			// All rated
			elseif ($filter['status'] == -1)
			{
				$select->where('(status' . $i . '.isLatest = 1 AND status' . $i . '.rating <> ?)' . $allowNull, 0);
			}
			// Anything (all of them)
			elseif ($filter['status'] == -2)
			{
				$select->where('status' . $i . '.isLatest = 1');
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

			// Filter by title
			if (isset($filter['withSource']) && $filter['withSource'])
			{
		    	$select->where('movie.source IS NOT NULL');
			}

			if ($maxDate)
				$maxDate = 'IF(`status' . $i . '`.`dateUpdate` IS NULL OR `status' . $i . '`.`dateUpdate` < ' . $maxDate . ', ' . $maxDate . ', `status' . $i . '`.`dateUpdate`)';
			else
				$maxDate = '`status' . $i . '`.`dateUpdate`';

	    	$i++;
		}

		$select->columns(array('date' => new Zend_Db_Expr($maxDate)));
		
    	return $select;
    }
}
