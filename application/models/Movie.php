<?php

class Default_Model_Movie extends Default_Model_AbstractModel
{
	static public function extractId($string)
	{
		$valid = preg_match_all("/(\d{7})/", $string, $r);
		if (isset($r[1][0]))
			return $r[1][0];
			
		return null;		
	}
	
	public function getTitle()
	{
		// If we didn't get the tilte yet, fetch it and save in out database
		if (!($this->title))
		{
			$file = @file_get_contents($this->getImdbUrl());
			$ok = preg_match("/<title>(.*)<\/title>/", $file, $m);
			if ($ok != 1)
				return '[title not available, could not fetch from IMDb]';
			
			$this->title = html_entity_decode($m[1], ENT_COMPAT, 'utf-8');
			$this->save();
		}
		
		return $this->title;
	}
	
	public function setId($id)
	{
		$this->id = self::extractId($id);
		return $this;
	}
	
	public function getImdbUrl()
	{
		return "http://www.imdb.com/title/tt" . $this->id . "/";
	}

	public function getStatus($idUser)
	{			
		$mapper = new Default_Model_StatusMapper();
		return $mapper->find($idUser, $this->id);
	}
	
	public function setStatus($idUser, $rating)
	{
		$mapper = new Default_Model_StatusMapper();
		$status = $mapper->find($idUser, $this->id);			
		$status->rating = $rating;
		$status->save();
	}
	
	public function getActivity()
	{
		$mapper = new Default_Model_StatusMapper();
		return $mapper->getActivityForMovie($this);
	}
}

?>