<?php

class Default_Model_StatusMapper extends Default_Model_AbstractMapper
{
    public function find($idUser, $idMovie)
    {
        $status = $this->getDbTable()->fetchRow("idUser='$idUser' AND idMovie='$idMovie'");
		
		if ($status == null)
		{
			$status = $this->getDbTable()->createRow();
			$status->idUser = $idUser;
			$status->idMovie = $idMovie;
        }
		
		return $status;
    }
    
    /**
     * Returns an array of Status containing all statuses for specified ids
     * (if they don't exist in database, they will be created with default values)
     * 
     * @param $idUser
     * @param $idMovies
     * @return array of Default_Model_Status
     */
    public function findAll($idUser, array $idMovies)
    {
    	$query = "idMovie='" . join("' OR idMovie='", $idMovies) . "'";
    	$query = "idUser='$idUser' AND ($query)";
    	$records = $this->getDbTable()->fetchAll($query);
    	
    	$statuses = array();
    	foreach($records as $record)
    	{
			$statuses[$record->idMovie] = $record;
    	}
    	
    	// Fill non existing statuses in databases
    	foreach ($idMovies as $id)
    	{
    		if (!array_key_exists($id, $statuses))
    		{
				$status = $this->getDbTable()->createRow();
				$status->idUser = $idUser;
				$status->idMovie = $id;
    			$statuses[$status->idMovie] = $status;
    		}
    	}
    	
    	return $statuses;
    } 
}

?>