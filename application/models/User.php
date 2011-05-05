<?php

class Default_Model_User extends Default_Model_AbstractModel
{
	/**
	 * The current user logged in.
	 * -1 before initialization
	 * null if no user logged in
	 * Default_Model_User if logged in
	 * @var -1|null|Default_Model_User 
	 */
	private static $currentUser = -1;
	
	/**
	 * Returns the user currently logged in or null
	 * @return null|Default_Model_User
	 */
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
	
	/**
	 * Set the user currently logged in, or log him out
	 * @param Default_Model_User $user
	 */
	public static function setCurrent(Default_Model_User $user = null)
	{
		$session = new Zend_Session_Namespace();
		$session->idUser = $user ? $user->id : null;
		self::$currentUser = $user;
	}
	
	/**
	 * Returns movie ratings statistics
	 * @return array of count of movies per ratings
	 */
	public function getStatistics()
	{
		return Default_Model_StatusMapper::getStatistics($this->id);
	}
}
