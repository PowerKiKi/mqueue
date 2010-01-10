<?php

class Default_Form_User extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the nickname element
        $this->addElement('text', 'nickname', array(
            'label'      => 'Nickname:',
            'required'   => true,
            'validators' => array(
                //array('validator' => 'Regex', 'options' => array("/(\d{7})/"))
                )
        ));
        
        // Add the email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email:',
            'required'   => true,
        	'filters' => array('filter' => array('filter' => 'stringTrim')),
            'validators' => array(
                array('validator' => 'emailAddress')
                )
        ));
        
        // Add the password ail element
        $this->addElement('password', 'password', array(
            'label'      => 'Password:',
            'required'   => true,
        	'filters' => array('filter' => array('filter' => 'stringTrim')),
            'validators' => array(
                //array('validator' => 'emailAddress')
                )
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Subscribe',
        ));

        // And finally add some CSRF protection
      //  $this->addElement('hash', 'csrf', array(
     //       'ignore' => true,
     //   ));
    }
}
