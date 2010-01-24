<?php

class Default_Model_User extends Default_Model_AbstractModel
{
	public static function getCurrent()
	{
		
	}
	
	public function getStatistics()
	{
		$mapper = new Default_Model_StatusMapper();
		return $mapper->getStatistics($this->id);
	}
}

?>