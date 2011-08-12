<?php

/**
 * This is the DbTable class for the movie table.
 */
class Default_Model_DbTable_Status extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'status';

	protected $_rowClass = 'Default_Model_Status';
}

