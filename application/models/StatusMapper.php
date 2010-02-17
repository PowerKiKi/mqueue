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
	
	public function getStatistics($idUser)
	{
		$select = $this->getDbTable()->select()->setIntegrityCheck(false)
			->from('status', 
				array(
				'rating' => 'IFNULL(rating, 0 )',
				'count' => 'count(IFNULL(rating, 0))'))
			->joinRight('movie', 
				'movie.id = status.idMovie AND status.idUser = '.$idUser, 
				array())
			->group('IFNULL(rating, 0)')
			;
			
		$records = $this->getDbTable()->fetchAll($select);
		
		// Set all count to 0
		$result = array('total' => 0, 'rated' => 0, Default_Model_Status::Nothing => 0);
		foreach (Default_Model_Status::$ratings as $val => $name)
		{
			$result[$val] = 0;
		}
		
		// Fetch real counts
		foreach ($records->toArray() as $row)		
		{
			$result[$row['rating']] = $row['count'];
			if ($row['rating'] != Default_Model_Status::Nothing)
			{
				$result['rated'] += $row['count'];
			}
			$result['total'] += $row['count'];
		}
		
		return $result;
	}

	private function prepareActivity($records)
	{
		$result = array();
		$mapperUser = new Default_Model_UserMapper();
		$mapperMovie = new Default_Model_MovieMapper();
		$cacheUser = array();
		$cacheMovie = array();
		foreach ($records as $r)
		{
			if (!array_key_exists($r->idUser, $cacheUser))
			{
				$cacheUser[$r->idUser] = $mapperUser->find($r->idUser);
			}
			$user = $cacheUser[$r->idUser]; 
			
			if (!array_key_exists($r->idMovie, $cacheMovie))
			{
				$cacheMovie[$r->idMovie] = $mapperMovie->find($r->idMovie);
			}
			$movie = $cacheMovie[$r->idMovie];
			
			$result []= array(
						'user' => $user,
						'movie' => $movie,
						'status' => $r,
			);
		}	

		return $result;
	}

	public function getActivity()
	{
		$select = $this->getDbTable()->select()
			->from('status')
			->order('dateUpdate DESC')
			->limit(200)
			;
			
		$records = $this->getDbTable()->fetchAll($select);
		$result = $this->prepareActivity($records);
		
		return $result;
	}

	public function getActivityForMovie(Default_Model_Movie $movie)
	{
		$select = $this->getDbTable()->select()
			->from('status')
			->where('idMovie = ?', $movie->id)
			->order('dateUpdate DESC')
			->limit(200)
			;
			
		$records = $this->getDbTable()->fetchAll($select);
		$result = $this->prepareActivity($records);
		
		return $result;
	}
	
	public function getActivityForUser(Default_Model_User $user)
	{
		$select = $this->getDbTable()->select()
			->from('status')
			->where('idUser = ?', $user->id)
			->order('dateUpdate DESC')
			->limit(200)
			;
			
		$records = $this->getDbTable()->fetchAll($select);
		$result = $this->prepareActivity($records);
		
		return $result;
	}
}

?>