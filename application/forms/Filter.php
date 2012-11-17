<?php

class Default_Form_Filter extends Zend_Form_SubForm
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
		
		$this->addElement('select', 'user', array(
			'multiOptions'   => $users,
			'label'	=> _tr('User :'),
			'required' => true,
			'class' => 'filterUser',
            'validators' => array(
        		array('validator' =>  new Default_Form_Validate_User()),
                ),
			'filters' => array(
				array('int')
			)
		));
		
		$status = array(-1 => _tr('<< rated >>'), 0 => _tr('<< no rated >>'), -2 => _tr('<< all >>'));
		$status = $status + Default_Model_Status::$ratings;
		
		$this->addElement('select', 'status', array(
			'multiOptions'   => $status,
			'label'	=> _tr('Rating :'),
			'required' => true,
			'class' => 'filterStatus',
			'filters' => array(
				array('int')
			)
		));
		
		
        // Add the title element
        $this->addElement('text', 'title', array(
            'label'      => _tr('Title :'),
			'filters' => array(
				array('stringTrim')
			)
        ));
		
        // Add the filter element
        $this->addElement('checkbox', 'withSource', array(
            'label'      => _tr('With source'),
        ));
		$this->withSource->getDecorator('Label')->setOptions(array('placement' => 'append'));
		

		$this->setDecorators(array(
				'FormElements',
				array(array('row' => 'HtmlTag'), array('tag' => 'dl', 'class' => 'filter')),				
				));
	}
    
	/**
	 * Disable extra field elements
	 */
    function disableExtraFields()
    {
    	$this->removeElement('title');
    	$this->removeElement('withSource');
    }

    /**
     * Override getValues() to replace special '0' value with current user
     * @return array values
     */
    public function getValues($suppressArrayNotation = false)
    {
    	$values = parent::getValues($suppressArrayNotation);
    	
    	if ($values['user'] == '0')
    	{
    		$values['user'] = Default_Model_User::getCurrent()->id;
    	}
    	
    	return $values;
    }
    
    /**
     * Returns values as readable text for end-user
     * @return string 
     */
    public function getValuesText()
    {
    	$text = '';
    	$values = $this->getValues(true);
    	
    	if (@$values['title'])
    		$text = _tr('title') . ':"' . $values['title'] .'" + ';
    	
    	$users = $this->getElement('user')->getMultiOptions();
    	$statuses = $this->getElement('status')->getMultiOptions();
    	
    	$text .= $users[$values['user']] . ':' . $statuses[$values['status']];
    		
    	return $text;
    }
	
}
