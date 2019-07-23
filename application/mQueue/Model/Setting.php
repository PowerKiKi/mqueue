<?php

namespace mQueue\Model;

/**
 * Settings stored in database (not to be confused with application configuration)
 */
class Setting extends AbstractModel
{
    /**
     * Returns the setting, with a default value set if none was found.
     *
     * @param string $id unique name of setting
     * @param mixed $defaultValue
     *
     * @return Setting
     */
    public static function get($id, $defaultValue)
    {
        $setting = SettingMapper::find($id, $defaultValue);

        return $setting;
    }

    /**
     * Defines the value of the setting
     *
     * @param string $id unique name of setting
     * @param string $value the value to be set
     */
    public static function set($id, $value): void
    {
        $setting = self::get($id, $value);
        $setting->value = $value;
        $setting->save();
    }
}
