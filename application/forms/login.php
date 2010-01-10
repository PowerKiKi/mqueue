<?php

class Default_Form_Login extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
       
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
            //    array('validator' => 'emailAddress')
                )
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Login',
        ));

        // And finally add some CSRF protection
      //  $this->addElement('hash', 'csrf', array(
     //       'ignore' => true,
     //   ));
    }
}
