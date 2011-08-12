<?php

abstract class Default_Model_SettingMapper extends Default_Model_AbstractMapper
{
	/**
	 * Returns the setting, with a default value set if none was found.
	 * @param string $id
	 * @param mixed $defaultValue
	 * @return Default_Model_Setting
	 */
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
