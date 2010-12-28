<?php

abstract class Default_Model_UserMapper extends Default_Model_AbstractMapper
{	
	/**
	 * Finds a user by its email and password (not hashed)
	 * @param string $email
	 * @param string $password
     * @return Default_Model_User|null
	 */
	public static function findEmailPassword($email, $password)
	{
		$select = self::getDbTable()->select()
			->where('email = ?', $email)
			->where('password = SHA1(?)', $password);
		
		$record = self::getDbTable()->fetchRow($select);
		
		return $record;
	}
	
	/**
	 * Finds a user by its ID
	 * @param integer $id
     * @return Default_Model_User|null
	 */
    public static function find($id)
    {
        $result = self::getDbTable()->find($id);
		
        return $result->current();
    }

    /**
     * Finds all users
     * @return Default_Model_User[]
     */
    public static function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll(null, 'LOWER(nickname)');
		
        return $resultSet;
    }
}

?>