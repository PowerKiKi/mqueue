<?php

class Default_Model_SettingMapper extends Default_Model_AbstractMapper
{

	public function find($id, $defaultValue)
	{
		$result = $this->getDbTable()->find($id)->current();

		if ($result == null)
		{
			$result = $this->getDbTable()->createRow();
			$result->id = $id;
			$result->value = $defaultValue;
		}

		return $result;
	}
}

?>