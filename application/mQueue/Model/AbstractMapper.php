<?php

namespace mQueue\Model;

use Zend_Db_Table_Abstract;

abstract class AbstractMapper
{
    private static $dbTables = [];

    /**
     * Returns the Zend_Db_Table for the current model class
     *
     * @return Zend_Db_Table_Abstract
     */
    public static function getDbTable()
    {
        $className = self::getShortClassName();
        $dbTableClassName = '\mQueue\Model\DbTable\\' . $className;
        if (!array_key_exists($dbTableClassName, self::$dbTables)) {
            $dbTable = new $dbTableClassName();
            self::$dbTables[$dbTableClassName] = $dbTable;
        }

        return self::$dbTables[$dbTableClassName];
    }

    /**
     * Returns the short class name of the model
     * eg: called from \mQueue\Model\ChapterMapper, it will return 'Chapter'
     *
     * @return string short class name
     */
    private static function getShortClassName()
    {
        $className = get_called_class();
        preg_match('/([^\\\\]*)Mapper$/', $className, $r);

        return $r[1];
    }
}
