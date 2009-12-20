<?php

class Default_Model_AbstractModel extends Zend_Db_Table_Row_Abstract
{
	private $mapper;
	
	protected function setMapper($mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    protected function getMapper()
    {
        if (null === $this->mapper) {
            $this->setMapper(new Default_Model_GuestbookMapper());
        }
        return $this->mapper;
    }
}

?>