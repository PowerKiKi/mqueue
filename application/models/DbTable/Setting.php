<?php

/**
 * This is the DbTable class for the setting table.
 */
class Default_Model_DbTable_Setting extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'setting';
	
	protected $_rowClass = 'Default_Model_Setting';
}

?>