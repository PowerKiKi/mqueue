<?php

class Default_Model_MovieMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Default_Model_DbTable_Movie');
        }
        return $this->_dbTable;
    }

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
        if (0 == count($result))
		{
            return null;
        }
		
        return $result->current();
        $movie = new Default_Model_Movie();
		$movie->setId($row->id)
                  ->setTitle($row->title);
				  
		return $movie;
    }

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row)
		{
            $entry = new Default_Model_Movie();
            $entry->setId($row->id)
                  ->setTitle($row->title);
					
            $entries[] = $entry;
        }
		
        return $entries;
    }
}

?>