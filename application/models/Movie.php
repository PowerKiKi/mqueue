<?php

/**
 * A movie
 */
class Default_Model_Movie extends Default_Model_AbstractModel
{
	static public function extractId($string)
	{
		$valid = preg_match_all("/(\d{7})/", $string, $r);
		if (isset($r[1][0]))
			return $r[1][0];
			
		return null;		
	}
	
	/**
	 * Returns the title, if needed fetch the title from IMDb 
	 * @return string
	 */
	public function getTitle()
	{
		// If we didn't get the tilte yet, fetch it and save in our database
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
	
	/**
	 * Sets the ID for the movie from any string containing a valid ID 
	 * @param string $id
	 * @return Default_Model_Movie
	 */
	public function setId($id)
	{
		$this->id = self::extractId($id);
		return $this;
	}
	
	/**
	 * Returns the IMDb url for the movie
	 * @return string
	 */
	public function getImdbUrl()
	{
		return "http://www.imdb.com/title/tt" . $this->id . "/";
	}

	/**
	 * Returns the status for this movie and the specified user
	 * @param integer $idUser
	 * @return Default_Model_Status
	 */
	public function getStatus($idUser)
	{
		return Default_Model_StatusMapper::find($idUser, $this->id);
	}
	
	/**
	 * Set the status for the specified user
	 * @param integer $idUser
	 * @param integer $rating @see Default_Model_Status
	 * @return null
	 */
	public function setStatus($idUser, $rating)
	{
		$status = Default_Model_StatusMapper::find($idUser, $this->id);			
		$status->rating = $rating;
		$status->save();
	}
	
	/**
	 * Returns an array of latest activities
	 * @return array of activties
	 */
	public function getActivity()
	{
		return Default_Model_StatusMapper::getActivityForMovie($this);
	}
}

?>