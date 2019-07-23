<?php

namespace mQueue\Model;

abstract class SettingMapper extends AbstractMapper
{
    /**
     * Returns the setting, with a default value set if none was found.
     *
     * @param string $id
     * @param mixed $defaultValue
     *
     * @return Setting
     */
    public static function find($id, $defaultValue)
    {
        $result = self::getDbTable()->find([$id])->current();

        if ($result == null) {
            $result = self::getDbTable()->createRow();
            $result->id = $id;
            $result->value = $defaultValue;
        }

        return $result;
    }
}
