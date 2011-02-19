<?php

class Default_Form_Filters extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to GET
		$this->setMethod('get');
		$this->setName('filters');

		
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
		
		$this->addDecorator('Fieldset');
		
		$this->setDecorators(array(
				'FormElements',
				array(array('fieldset' => 'Fieldset'), array('legend' => 'Filter')),
				'Form',
			));
		
		$this->addDisplayGroup(array('submit', 'clear'), 'filters', array('legend' => _tr('Filter')));

		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
				
		$this->addElement('image', 'addFilter', array(
			'src' => $view->serverUrl() . $view->baseUrl('/images/add.png'),
			'imageValue' => '1',
		));
		$this->addDisplayGroup(array('addFilter'), 'addFilterGroup', array('class' => 'addFilter'));
		
		
		$this->setDisplayGroupDecorators(array(
				'FormElements',
				array(array('row' => 'HtmlTag'), array('tag' => 'dl', 'class' => 'buttons')),				
				));
	}
	
	/**
	 * Overrides isValid to dynamically generate subforms which will be used for validation
	 * @param array $data
	 */
	public function isValid($data)
	{
		$this->setDefaults($data);
		return parent::isValid($data);
	}

    /**
     * Override setDefaults to dynamically generate subforms
     * Will add a subform per filter.
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $keys = array_keys($defaults);
        $position = 1;
        $max = 0;
        foreach ($keys as $key)
        {
            if (preg_match('/^filter(\d)+$/', $key, $m))
            {
            	$subform = new Default_Form_Filter();
            	if ($position > 1)
            	{
            		$subform->disableTitle();
            	}
				$this->addSubForm($subform, $key, $position++);
				if ($m[1] > $max)
					$max = $m[1];
            }
        }
        
        $image = $this->getElement('addFilter');
        
        if ((isset($defaults['addFilter']) && $defaults['addFilter'] == 1) || $max == 0)
        {
        	$subform = new Default_Form_Filter();
        	
        	if ($max != 0)
        	{
        		$subform->disableTitle();
        	}
        	
			$this->addSubForm($subform, 'filter' . ($max + 1), $position++);
        }
        $defaults['addFilter'] = 0;
        
        // set defaults, which will propagate to newly created subforms
        return parent::setDefaults($defaults);
    }
    
    public function getValuesText()
    {
    	$text = array();
    	foreach ($this->getSubForms() as $subForm)
    	{
    		$text []= $subForm->getValuesText();
    	}
    	
    	return join(' + ', $text);
    }
}
