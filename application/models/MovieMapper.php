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
    
    public static function getFilteredQuery($idUser, $status, $title, $sort, $sortOrder)
    {
    	$sortable = array('title', 'rating');
    	if (!in_array($sort, $sortable))
    		$sort = reset($sortable);
    	
    	if ($sortOrder == 'desc')
    		$sortOrder = 'DESC';
    	else
    		$sortOrder = 'ASC';

    	$allowNull = ($status == 0 || $status == -2 ? ' OR status.idUser IS NULL' : '');
		$select = self::getDbTable()->select()
			->from('movie')
			->joinLeft('status', '(movie.id = status.idMovie AND status.idUser = ' . $idUser . ')' . $allowNull, array())
			->order($sort . ' ' . $sortOrder);

		// Filter by status
		if ($status >= 0 && $status <= 5)
		{
			$select->where('status.rating = ?' . $allowNull, $status);
		}
		elseif ($status == -1)
		{
			$select->where('status.rating <> ?' . $allowNull, 0);
		}
		
		// Filter by title
		$titles = explode(' ', trim($title));
    	foreach ($titles as $part)
    	{
    		if ($part = trim($part))
    			$select->where('movie.title LIKE ?', '%' . $part . '%');
    	}
    	
    	return $select;
    }
}

?>