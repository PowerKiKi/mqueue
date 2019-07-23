<?php

namespace mQueue\Form\Validate;

use Zend_Validate_Db_RecordExists;

/**
 * Validator for User ID.
 */
class User extends Zend_Validate_Db_RecordExists
{
    public function __construct()
    {
        parent::__construct(['table' => 'user', 'field' => 'id']);
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains a valid User ID
     *
     * @param int $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        return ($value === 0) || parent::isValid($value);
    }
}
