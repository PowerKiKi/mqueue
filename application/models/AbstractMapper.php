<?php
 
class Default_Model_AbstractMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
        	$className = get_class($this);
        	preg_match("/([^_]*)Mapper/", $className, $r);
        	
            $this->setDbTable('Default_Model_DbTable_' . $r[1]);
        }
        return $this->_dbTable;
    }
}
 
?>