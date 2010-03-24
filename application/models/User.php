<?php

class Default_Model_User extends Default_Model_AbstractModel
{
	private static $currentUser = -1;
	public static function getCurrent()
	{
		if (is_integer(self::$currentUser))
		{
			$session = new Zend_Session_Namespace();
			if (isset($session->idUser))
			{
				self::$currentUser = Default_Model_UserMapper::find($session->idUser);
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
		return Default_Model_StatusMapper::getStatistics($this->id);
	}

	public function getActivity()
	{
		return Default_Model_StatusMapper::getActivityForUser($this);
	}
}

?>