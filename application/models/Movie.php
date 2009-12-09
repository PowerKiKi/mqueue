<?php

class Default_Model_Movie extends Default_Model_AbstractModel
{
	private $id;
	private $title;
	private $imdb;
	private $status;
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
	
	public function getImdbUrl()
	{
		return "http://www.imdb.com/title/tt" . $this->imdb . "/";
	}
	
	public function setImdb($imdb)
	{
		$this->imdb = (int)$imdb;
		return $this;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function setStatus($status)
	{
		$this->status = (int)$status;
		return $this;
	}
}

?>