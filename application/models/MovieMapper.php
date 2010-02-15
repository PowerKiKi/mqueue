<?php

class Default_Model_MovieMapper extends Default_Model_AbstractMapper
{
    public function find($id)
    {
		
        $result = $this->getDbTable()->find($id);
		
        return $result->current();
    }

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
		
        return $resultSet;
    }
    
    public function getFilteredQuery($idUser, $status, $sort, $sortOrder)
    {
    	$sortable = array('title', 'rating');
    	if (!in_array($sort, $sortable))
    		$sort = reset($sortable);
    	
    	if ($sortOrder == 'desc')
    		$sortOrder = 'DESC';
    	else
    		$sortOrder = 'ASC'; 
    	
    	
		$select = $this->getDbTable()->select()
			->from('movie')
			->joinLeft('status', 'movie.id = status.idMovie AND status.idUser = ' . $idUser , array())
			->where('status.idUser = ?', $idUser)
			->order($sort . ' ' . $sortOrder);
			
		if ($status >= 0 && $status <= 5)
		{
			$select->where('status.rating = ?', $status);
		}
		elseif ($status == -1)
		{
			$select->where('status.rating <> ?', 0);
		}
		
		
		if ($status == 0 || $status == -2)
		{
			$select->orWhere('status.rating IS NULL');
		}
		
    	return $select;
    }
}

?>