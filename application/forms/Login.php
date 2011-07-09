<?php

class Default_Form_Login extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
       
        // Add the email element
        $this->addElement('text', 'email', array(
            'label'      => _tr('Email:'),
            'required'   => true,
        	'filters' => array('filter' => array('filter' => 'stringTrim')),
            'validators' => array(
                array('validator' => 'emailAddress')
                )
        ));
        
        // Add the password element
        $this->addElement('password', 'password', array(
            'label'      => _tr('Password:'),
            'required'   => true,
        	'filters' => array('filter' => array('filter' => 'stringTrim')),
        ));
		
		// Checkbox to remember user
		$this->addElement('checkbox', 'remember', array(
			'label' => _tr('Remember me'),
		));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => _tr('Login'),
        ));

        // And finally add some CSRF protection
      //  $this->addElement('hash', 'csrf', array(
     //       'ignore' => true,
     //   ));
    }
}
