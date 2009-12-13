<?php

class Default_Model_Movie extends Default_Model_AbstractModel
{
	private $id;
	private $title;
	private $date_update;
	
	
    public function __construct(array $options = null)
    {
		parent::__construct($options);
    }
	
	public function getTitle()
	{
		if (!isset($this->title))
		{
			$file = file_get_contents($this->getImdbUrl());
			$ok = preg_match("/<title>(.*)<\/title>/", $file, $m);
			if ($ok != 1)
				throw new Exception("could not find movie with ID ='$this->id' '$ok' $this->imdbUrl");
			
			$this->title = html_entity_decode($m[1], ENT_COMPAT, 'utf-8');
		}
		
		return $this->title;
	}
	
	public function setTitle($title)
	{
		$this->title = (string)$title;
		return $this;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($id)
	{
		$valid = preg_match_all("/(\d{7})/", $id, $r);
		if (isset($r[1][0]))
			$this->id = $r[1][0];
			
		return $this;
	}
	
	public function getImdbUrl()
	{
		return "http://www.imdb.com/title/tt" . $this->id . "/";
	}
}

?>