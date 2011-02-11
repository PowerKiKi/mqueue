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
    	elseif ($sort != 'date')
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
		foreach ($filters as $key => $filter)
		{
			if (!preg_match('/^filter\d+$/', $key))
				continue;
				
			$allowNull = ($filter['status'] == 0 || $filter['status'] == -2 ? ' OR status' . $i . '.idUser IS NULL' : '');
			$select->joinLeft(array('status' . $i => 'status'), '(movie.id = status' . $i . '.idMovie AND status' . $i . '.idUser = ' . $filter['user'] . ')' . $allowNull, array());
				
			// Filter by status
			if ($filter['status'] >= 0 && $filter['status'] <= 5)
			{
				$select->where('status' . $i . '.rating = ?' . $allowNull, $filter['status']);
			}
			elseif ($filter['status'] == -1)
			{
				$select->where('status' . $i . '.rating <> ?' . $allowNull, 0);
			}
			
			// Filter by title
			if (isset($filter['title']))
			{
				$titles = explode(' ', trim($filter['title']));
		    	foreach ($titles as $part)
		    	{
		    		if ($part = trim($part))
		    			$select->where('movie.title LIKE ?', '%' . $part . '%');
		    	}
			}
			
			if ($maxDate)
				$maxDate = 'IF(`status' . $i . '`.`dateUpdate` > ' . $maxDate . ', `status' . $i . '`.`dateUpdate` , ' . $maxDate . ')';
			else
				$maxDate = '`status' . $i . '`.`dateUpdate`';
	    	
	    	$i++;
		}
		
		$select->columns(array('date' => $maxDate));
		
    	return $select;
    }
}

?>