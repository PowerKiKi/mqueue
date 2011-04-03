<?php

/**
 * Validator for geolocation data. The data is array encoded in JSON.
 * The array is as follow:
 * 
 * $data['type'] the type of value. Possible values: npa, city, region, country
 * $data['value'] the value itself which always is a geonamesId from geonames.org
 * $data['lat'] only if type is 'npa': lattitude
 * $data['lng'] only if type is 'npa': longitude
 */
class Default_Form_Validate_User extends Zend_Validate_Db_RecordExists
{
    public function __construct()
    {
    	parent::__construct(array('table' => 'user', 'field' => 'id'));
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains a valid geolocation ID
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
    	return ($value == 0) || parent::isValid($value);
    }
}
