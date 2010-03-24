<?php

class Default_Form_Filter extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to GET
		$this->setMethod('get');

		
		$users = array();
		if (Default_Model_User::getCurrent())
		{
			$users = array(0 => _tr('<< me >>'));
		}
		
		foreach (Default_Model_UserMapper::fetchAll() as $user)
			$users[$user->id] = $user->nickname;
		
		$this->addElement('select', 'filterUser', array(
			'multiOptions'   => $users,
			'label'	=> _tr('User :'),
		));
		
		
		$status = array(-1 => _tr('<< rated >>'), 0 => _tr('<< no rated >>'), -2 => _tr('<< all >>'));
		$status = $status + Default_Model_Status::$ratings;
		
		$this->addElement('select', 'filterStatus', array(
			'multiOptions'   => $status,
			'label'	=> _tr('Rating :'),
		));
		
		
        // Add the filter element
        $this->addElement('text', 'filterTitle', array(
            'label'      => 'Title :',
        ));
		

		// Add the submit button
		$this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => _tr('Apply'),
		));

		// Add the submit button
		$this->addElement('submit', 'clear', array(
            'ignore'   => true,
            'label'    => _tr('Clear'),
		));
		
		
		$this->addDisplayGroup(array('filterUser', 'filterStatus', 'filterTitle', 'submit', 'clear'), 'filter', array('legend' => _tr('Filter')));
	}
}
