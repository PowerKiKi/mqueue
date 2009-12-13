<?php

class Default_Model_StatusMapper
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
            $this->setDbTable('Default_Model_DbTable_Status');
        }
        return $this->_dbTable;
    }

    public function save(Default_Model_Status $status)
    {
		$movieMapper = new Default_Model_MovieMapper();
		$movie = $movieMapper->find($status->idMovie);
		
		if ($movie == null)
		{
			$movie = new Default_Model_Movie();
			$movie->id = $status->idMovie;
			
			$movieMapper->save($movie);	
		}
	
        $data = array(
            'id'   => $status->getId(),
            'id_user'   => $status->getIdUser(),
            'id_movie'   => $status->getIdMovie(),
            'rating'   => $status->getRating()
        );

        if (null === ($id = $status->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($id_user, $id_movie)
    {
		$status = new Default_Model_Status();
        $row = $this->getDbTable()->fetchRow("id_user='$id_user' AND id_movie='$id_movie'");


		if ($row == null)
		{
			$status->setIdUser($id_user);
			$status->setIdMovie($id_movie);
        }
		else
		{
		
			$status->setId($row->id)
				  ->setIdUser($row->id_user)
				  ->setIdMovie($row->id_movie)
				  ->setRating($row->rating);
		}
				  
		return $status;
    }

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row)
		{
            $entry = new Default_Model_Status();
            $entry->setId($row->id)
                  ->setIdUser($row->id_user)
                  ->setIdMovie($row->id_movie)
                  ->setRating($row->rating)
                  ;//->setMapper($this);
            $entries[] = $entry;
        }
		
        return $entries;
    }
}

?>