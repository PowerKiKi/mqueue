<?php

class Default_Model_UserMapper extends Default_Model_AbstractMapper
{
	public function findEmailPassword($email, $password)
	{
		$select = $this->getDbTable()->select()
			->where('email = ?', $email)
			->where('password = ?', $password);
		
		$record = $this->getDbTable()->fetchRow($select);
		
		return $record;
	}
	
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
		
        return $result->current();
    }

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
		
        return $resultSet;
    }
}

?>