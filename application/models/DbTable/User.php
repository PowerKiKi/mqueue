<?php

/**
 * This is the DbTable class for the user table.
 */
class Default_Model_DbTable_User extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'user';
	
	protected $_rowClass = 'Default_Model_User';
}

?>