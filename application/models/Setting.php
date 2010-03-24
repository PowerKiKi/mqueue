<?php

class Default_Model_Setting extends Default_Model_AbstractModel
{
	public static function get($id, $defaultValue)
	{
        $setting = Default_Model_SettingMapper::find($id, $defaultValue);
        
        return $setting;
	}
	
	public static function set($id, $value)
	{
		$setting = self::get($id, $value);
		$setting->value = $value;
		$setting->save();
	}
}

?>