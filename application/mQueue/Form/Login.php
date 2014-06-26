<?php

namespace mQueue\Form;

use Zend_Form;

class Login extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the email element
        $this->addElement('text', 'email', array(
            'label' => _tr('Email:'),
            'autofocus' => true,
            'required' => true,
            'filters' => array('filter' => array('filter' => 'stringTrim')),
            'validators' => array(
                array('validator' => 'emailAddress')
            )
        ));

        // Add the password element
        $this->addElement('password', 'password', array(
            'label' => _tr('Password:'),
            'required' => true,
            'filters' => array('filter' => array('filter' => 'stringTrim')),
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _tr('Login'),
        ));

        // Add referrer to redirect after login
        $this->addElement('hidden', 'referrer');
    }

}
