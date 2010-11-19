<?php

/**
 * A movie
 */
class Default_Model_Movie extends Default_Model_AbstractModel
{
	/**
	 * All known IMDb hostnames indexed by their language
	 * @var array
	 */
	public static $imdbHostnames = array(
			'en' => 'www.imdb.com',
			'fr' => 'www.imdb.fr',
			'de' => 'www.imdb.de',
			'es' => 'www.imdb.es',
			'it' => 'www.imdb.it',
			'pt' => 'www.imdb.pt',
			'akas' => 'akas.imdb.com',
		);
	
	/**
	 * Extract IMDb id from URL
	 * @param string $string
	 * @return null|string the id extracted
	 */
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
		// If we didn't get the title yet, fetch it and save in our database
		if (!($this->title))
		{
			$file = @file_get_contents($this->getImdbUrl('akas'));

			$document = new DOMDocument();
			@$document->loadHTML($file);
			$xpath = new DOMXPath($document);
			$query = '//meta[contains(@property, "og:title")]/@content';

			$entries = $xpath->evaluate($query);
			if ($entries->length != 1)
			{
				return '[title not available, could not fetch from IMDb]';
			}
			
			$this->title = $entries->item(0)->value;
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
	 * @param string suggested language for hostname
	 * @return string
	 */
	public function getImdbUrl($lang = null)
	{
		if ($lang == null)
		{
			$lang = Zend_Registry::get('Zend_Locale')->getLanguage();
		}
		
		if (isset(Default_Model_Movie::$imdbHostnames[$lang]))
			$hostname = Default_Model_Movie::$imdbHostnames[$lang];
		else
			$hostname = reset(Default_Model_Movie::$imdbHostnames);
		
		return 'http://' . $hostname . '/title/tt' . $this->id . '/';
	}

	/**
	 * Returns the status for this movie and the specified user
	 * @param Default_Model_User $user
	 * @return Default_Model_Status
	 */
	public function getStatus(Default_Model_User $user = null)
	{
		return Default_Model_StatusMapper::find($user ? $user->id : null, $this->id);
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
}

?>