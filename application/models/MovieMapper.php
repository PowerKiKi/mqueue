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
            'imdb'   => $movie->getImdb(),
            'status' => $movie->getStatus()
        );

        if (null === ($id = $movie->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($id)
    {
		$movie = new Default_Model_Movie();
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
		{
            return;
        }
		
        $row = $result->current();
        $movie->setId($row->id)
                  ->setImdb($row->imdb)
                  ->setStatus($row->status);
				  
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
                  ->setImdb($row->imdb)
                  ->setStatus($row->status)
                  ->setTitle($row->title)
                  ;//->setMapper($this);
            $entries[] = $entry;
        }
		
        return $entries;
    }
}

?>