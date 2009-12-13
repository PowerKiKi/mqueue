<?php

class Default_Model_Movie extends Default_Model_AbstractModel
{
	private $id;
	private $title;
	private $imdb;
	private $date_update;
	
	
    public function __construct(array $options = null)
    {
		parent::__construct($options);
    }
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($id)
	{
		$this->id = (int)$id;
		return $this;
	}
	
	
	public function getTitle()
	{
		if (!isset($this->title))
		{
			$file = file_get_contents($this->getImdbUrl());
			preg_match("/<h1>(.*) <span>/", $file, $m);

			$this->title = $m[1];
		}
		
		return $this->title;
	}
	
	public function setTitle($title)
	{
		$this->title = (string)$title;
		return $this;
	}
	
	public function getImdb()
	{
		return $this->imdb;
	}
	
	public function setImdb($imdb)
	{
		$valid = preg_match_all("/(\d{7})/", $imdb, $r);
		if (isset($r[1][0]))
			$this->imdb = $r[1][0];
		return $this;
	}
	
	public function getImdbUrl()
	{
		return "http://www.imdb.com/title/tt" . $this->imdb . "/";
	}
}

?>