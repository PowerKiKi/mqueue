<?php

/**
 * Settings stored in database (not to be confused with application configuration)
 *
 */
class Default_Model_Setting extends Default_Model_AbstractModel
{
	/**
	 * Returns the setting, with a default value set if none was found.
	 * @param string $id unique name of setting
	 * @param mixed $defaultValue
	 * @return Default_Model_Setting
	 */
	public static function get($id, $defaultValue)
	{
        $setting = Default_Model_SettingMapper::find($id, $defaultValue);
        
        return $setting;
	}
	
	/**
	 * Defines the value of the setting
	 * @param string $id unique name of setting
	 * @param string $value the value to be set
	 */
	public static function set($id, $value)
	{
		$setting = self::get($id, $value);
		$setting->value = $value;
		$setting->save();
	}
}

?>