<?php

namespace mQueue\Model\DbTable;

use Zend_Db_Table_Abstract;

/**
 * This is the DbTable class for the user table.
 */
class User extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'user';
    protected $_rowClass = \mQueue\Model\User::class;
}
