<?php

class Default_Model_Status extends Default_Model_AbstractModel
{
	const Nothing = 0;
	const Need = 1;
	const Bad = 2;
	const Ok = 3;
	const Excellent = 4;
	const Favorite = 5;
	
	public static $ratings = array(
		 'Need' => Default_Model_Status::Need,
		 'Bad' => Default_Model_Status::Bad,
		 'Ok' => Default_Model_Status::Ok,
		 'Excellent' => Default_Model_Status::Excellent,
		 'Favorite' => Default_Model_Status::Favorite);
	
	
	private $id;
	private $idUser;
	private $idMovie;
	private $rating;
	
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
		$this->id = $id;
		return $this;
	}
	
	public function getIdUser()
	{
		return $this->idUser;
	}
	
	public function setIdUser($idUser)
	{
		$this->idUser = $idUser;
		return $this;
	}
	
	public function getIdMovie()
	{
		return $this->idMovie;
	}
	
	public function setIdMovie($idMovie)
	{
		$this->idMovie = $idMovie;
		return $this;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function getMovie()
	{
		return $this->movie;
	}
	
	public function getRating()
	{
		return $this->rating;
	}
	
	public function setRating($rating)
	{
		$this->rating = $rating;
		return $rating;
	}
	
}

?>