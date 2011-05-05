<?php

/**
 * This is the DbTable class for the movie table.
 */
class Default_Model_DbTable_Movie extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'movie';
	
	protected $_rowClass  = 'Default_Model_Movie';
}

