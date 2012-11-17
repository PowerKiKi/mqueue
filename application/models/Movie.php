<?php

/**
 * A movie
 */
class Default_Model_Movie extends Default_Model_AbstractModel
{
	/**
	 * All known IMDb hostnames indexed by their language
	 * @var array $imdbHostnames
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

			$this->setReadOnly(false); // If the movie is coming from a joined query, we need to set non-readonly before saving
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
		$extractedId = self::extractId($id);
		if (!$extractedId)
		{
			throw new Exception(sprintf('Invalid Id for movie. Given "%1$s", extracted "%2$s"', $id, $extractedId));
		}

		$this->id = $extractedId;
		return $this;
	}

	/**
	 * Returns the IMDb url for the movie
	 * @param string $lang suggested language for hostname
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
		return Default_Model_StatusMapper::find($this->id, $user);
	}

	/**
	 * Set the status for the specified user
	 * @param Default_Model_User $user
	 * @param integer $rating @see Default_Model_Status
	 * @return Default_Model_Status
	 */
	public function setStatus(Default_Model_User $user, $rating)
	{
		$status = Default_Model_StatusMapper::set($this, $user, $rating);

		return $status;
	}

	/**
	 * Set the source for the movie if any. In any case record the search date and count
	 * @param array|false $source
	 */
	public function setSource($source)
	{
		
		$this->dateSearch = Zend_Date::now()->get(Zend_Date::ISO_8601);
		$this->searchCount++;
		if ($source && @$source['score'])
		{
			$this->identity = $source['identity'];
			$this->quality = $source['quality'];
			$this->score = $source['score'];
			$this->source = $source['link'];
		}
	}
}
