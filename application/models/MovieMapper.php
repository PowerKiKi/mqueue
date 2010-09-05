<?php

abstract class Default_Model_MovieMapper extends Default_Model_AbstractMapper
{
    public static function find($id)
    {
		
        $result = self::getDbTable()->find($id);
		
        return $result->current();
    }

    public static function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll();
		
        return $resultSet;
    }
    
    public static function getFilteredQuery($filters, $sort, $sortOrder)
    {
    	$sortable = array('title', 'rating');
    	if (!in_array($sort, $sortable))
    		$sort = reset($sortable);
    	
    	if ($sortOrder == 'desc')
    		$sortOrder = 'DESC';
    	else
    		$sortOrder = 'ASC';

    	
		$select = self::getDbTable()->select()
			->from('movie')
			->order($sort . ' ' . $sortOrder);

		$i = 0;
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
			$titles = explode(' ', trim($filter['title']));
	    	foreach ($titles as $part)
	    	{
	    		if ($part = trim($part))
	    			$select->where('movie.title LIKE ?', '%' . $part . '%');
	    	}
	    	
	    	$i++;
		}
		
    	return $select;
    }
}

?>