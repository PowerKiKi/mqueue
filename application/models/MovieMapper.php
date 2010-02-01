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
    
    public function getFilteredQuery($idUser, $status)
    {
		$select = $this->getDbTable()->select()
			->from('movie')
			->joinLeft('status', 'movie.id = status.idMovie' , array())
			->where('status.idUser = ?', $idUser);
			
		if ($status >= 0 && $status <= 5)
		{
			$select->where('status.rating = ?', $status);
		}
		else
		{
			$select->where('status.rating <> ?', 0);
		}
		
		
		if ($status == 0)
		{
			$select->orWhere('status.idUser IS NULL');
			$select->orWhere('status.rating IS NULL');
		}
		
    	return $select;
    }
}

?>