<?php

class Default_Form_User extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the nickname element
        $this->addElement('text', 'nickname', array(
            'label'      => _tr('Nickname:'),
            'required'   => true,
        	'filters' => array('filter' => array('filter' => 'stringTrim')),
            'validators' => array(
        		array('validator' =>  new Zend_Validate_Db_NoRecordExists(array('table' => 'user', 'field' => 'nickname'))),
                )
        ));
        
        // Add the email element
        $this->addElement('text', 'email', array(
            'label'      => _tr('Email:'),
            'required'   => true,
        	'filters' => array('filter' => array('filter' => 'stringTrim')),
            'validators' => array(
                array('validator' => 'emailAddress'),
                array('validator' =>  new Zend_Validate_Db_NoRecordExists(array('table' => 'user', 'field' => 'email'))),
                )
        ));
        
        // Add the password ail element
        $this->addElement('password', 'password', array(
            'label'      => _tr('Password:'),
            'required'   => true,
            'validators' => array(
                //array('validator' => 'emailAddress')
                )
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => _tr('Subscribe'),
        ));

        // And finally add some CSRF protection
		$this->addElement('hash', 'csrf', array(
			'ignore' => true,
			'decorators' => array('ViewHelper'),
		));
    }
}
