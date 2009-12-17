<?php

class Default_Model_StatusMapper extends Default_Model_AbstractMapper
{
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
        $status = $this->getDbTable()->fetchRow("idUser='$idUser' AND idMovie='$idMovie'");
		
		if ($status == null)
		{
			$status = new Default_Model_Status();
			$status->setIdUser($idUser);
			$status->setIdMovie($idMovie);
        }
		
		return $status;
    }
}

?>