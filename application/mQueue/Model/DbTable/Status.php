<?php

namespace mQueue\Model\DbTable;

use Zend_Db_Table_Abstract;

/**
 * This is the DbTable class for the movie table.
 */
class Status extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'status';
    protected $_rowClass = \mQueue\Model\Status::class;
}
