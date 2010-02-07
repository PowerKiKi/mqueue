<?php

class Default_Model_User extends Default_Model_AbstractModel
{
	private static $currentUser = -1;
	public static function getCurrent()
	{
		if (is_integer(self::$currentUser))
		{
			$mapper = new Default_Model_UserMapper();
		
			$session = new Zend_Session_Namespace();
			if (isset($session->idUser))
			{
				self::$currentUser = $mapper->find($session->idUser);
			}
			else
			{
				self::$currentUser = null;
			}
		}
		
		return self::$currentUser;
	}
	
	public function getStatistics()
	{
		$mapper = new Default_Model_StatusMapper();
		return $mapper->getStatistics($this->id);
	}

	public function getActivity()
	{
		$mapper = new Default_Model_StatusMapper();
		return $mapper->getActivityForUser($this);
	}
}

?>