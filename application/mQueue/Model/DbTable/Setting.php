<?php

namespace mQueue\Model\DbTable;

use Zend_Db_Table_Abstract;

/**
 * This is the DbTable class for the setting table.
 */
class Setting extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'setting';
    protected $_rowClass = \mQueue\Model\Setting::class;
}
