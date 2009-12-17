<?php

class Default_Model_MovieMapper extends Default_Model_AbstractMapper
{
    public function save(Default_Model_Movie $movie)
    {
        $data = array(
            'id'   => $movie->getId(),
            'title'   => $movie->getTitle(),
        );

            
        $this->getDbTable()->insert($data);
        
    }

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
}

?>