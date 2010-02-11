<?php

class Default_Model_Status extends Default_Model_AbstractModel
{
	const Nothing = 0;
	const Need = 1;
	const Bad = 2;
	const Ok = 3;
	const Excellent = 4;
	const Favorite = 5;
	
	public static $ratings = null; 
		 
	public function getUniqueId()
	{
		return $this->idMovie . '_' . $this->idUser;
	}
	
	public function getName()
	{
		if ($this->rating == 0)
			return _tr('Not rated');
		return Default_Model_Status::$ratings[$this->rating];
	}
}

Default_Model_Status::$ratings = array(
		 Default_Model_Status::Need => _tr('Need'),
		 Default_Model_Status::Bad => _tr('Bad'),
		 Default_Model_Status::Ok => _tr('Ok'),
		 Default_Model_Status::Excellent => _tr('Excellent'),
		 Default_Model_Status::Favorite => _tr('Favorite'));
?>