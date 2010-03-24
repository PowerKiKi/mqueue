<?php

abstract class Default_Model_UserMapper extends Default_Model_AbstractMapper
{
	public static function findNickname($nickname)
	{
		$select = self::getDbTable()->select()
			->where('nickname = ?', $nickname);
		
		$record = self::getDbTable()->fetchRow($select);
		
		return $record;
	}
	
	public static function findEmailPassword($email, $password)
	{
		$select = self::getDbTable()->select()
			->where('email = ?', $email)
			->where('password = SHA1(?)', $password);
		
		$record = self::getDbTable()->fetchRow($select);
		
		return $record;
	}
	
    public static function find($id)
    {
        $result = self::getDbTable()->find($id);
		
        return $result->current();
    }

    public static function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll(null, 'LOWER(nickname)');
		
        return $resultSet;
    }
}

?>