<?php

abstract class Default_Model_AbstractMapper
{

    private static $dbTables = array();

    /**
     * Returns the DbTable for the model mapper calling this method
     * @return Zend_Db_Table_Abstract
     */
    public static function getDbTable()
    {
        $className = get_called_class();
        if (!array_key_exists($className, self::$dbTables)) {
            preg_match("/([^_]*)Mapper/", $className, $r);
            $dbTableClassName = 'Default_Model_DbTable_' . $r[1];
            $dbTable = new $dbTableClassName();
            self::$dbTables[$className] = $dbTable;
        }

        return self::$dbTables[$className];
    }

}
