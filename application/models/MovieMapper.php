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
    
    public function getFilteredQuery($idUser)
    {
		$select = $this->getDbTable()->select()
			->from('movie')
			->joinLeft('status', 'movie.id = status.idMovie' , array())
			->where('status.idUser = ?', $idUser);
			
    	return $select;
    }
}

?>