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
}

?>