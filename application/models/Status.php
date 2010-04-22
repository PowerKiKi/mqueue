<?php

/**
 * A status (link between movie and user with a rating)
 */
class Default_Model_Status extends Default_Model_AbstractModel
{
	const Nothing = 0;
	const Need = 1;
	const Bad = 2;
	const Ok = 3;
	const Excellent = 4;
	const Favorite = 5;
	
	/**
	 * array of ratings names indexed by the rating value
	 * @var array
	 */
	public static $ratings = null; 
		 
	/**
	 * Returns the unique ID for this status to be used in HTML
	 * @return string
	 */
	public function getUniqueId()
	{
		return $this->idMovie . '_' . $this->idUser;
	}
	
	/**
	 * Returns the name
	 * @return string
	 */
	public function getName()
	{
		if ($this->rating == 0)
			return _tr('Not rated');
		return Default_Model_Status::$ratings[$this->rating];
	}
	
	/**
	 * Returns the date of last udpate
	 * @return Zend_Date
	 */
	public function getDateUpdate()
	{
		return new Zend_Date($this->dateUpdate, Zend_Date::ISO_8601);
	}
}

// Defines ratings names
Default_Model_Status::$ratings = array(
		 Default_Model_Status::Need => _tr('Need'),
		 Default_Model_Status::Bad => _tr('Bad'),
		 Default_Model_Status::Ok => _tr('Ok'),
		 Default_Model_Status::Excellent => _tr('Excellent'),
		 Default_Model_Status::Favorite => _tr('Favorite'));
?>