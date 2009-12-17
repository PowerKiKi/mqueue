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
            'idUser'   => $status->getIdUser(),
            'idMovie'   => $status->getIdMovie(),
            'rating'   => $status->getRating()
        );

        if (null === ($id = $status->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($idUser, $idMovie)
    {
		$status = new Default_Model_Status();
        $row = $this->getDbTable()->fetchRow("idUser='$idUser' AND idMovie='$idMovie'");


		if ($row == null)
		{
			$status->setIdUser($idUser);
			$status->setIdMovie($idMovie);
        }
		else
		{
		
			$status->setId($row->id)
				  ->setIdUser($row->idUser)
				  ->setIdMovie($row->idMovie)
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
                  ->setIdUser($row->idUser)
                  ->setIdMovie($row->idMovie)
                  ->setRating($row->rating)
                  ;//->setMapper($this);
            $entries[] = $entry;
        }
		
        return $entries;
    }
}

?>