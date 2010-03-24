<?php

abstract class Default_Model_SettingMapper extends Default_Model_AbstractMapper
{

	public static function find($id, $defaultValue)
	{
		$result = self::getDbTable()->find($id)->current();

		if ($result == null)
		{
			$result = self::getDbTable()->createRow();
			$result->id = $id;
			$result->value = $defaultValue;
		}

		return $result;
	}
}

?>