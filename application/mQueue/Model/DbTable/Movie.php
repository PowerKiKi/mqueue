<?php

namespace mQueue\Model\DbTable;

use Zend_Db_Table_Abstract;

/**
 * This is the DbTable class for the movie table.
 */
class Movie extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'movie';
    protected $_rowClass = \mQueue\Model\Movie::class;
}
