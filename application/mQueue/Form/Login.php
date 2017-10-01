<?php

namespace mQueue\Form;

use Zend_Form;

class Login extends Zend_Form
{
    public function init(): void
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the email element
        $this->addElement('text', 'email', [
            'label' => _tr('Email:'),
            'autofocus' => true,
            'required' => true,
            'filters' => ['filter' => ['filter' => 'stringTrim']],
            'validators' => [
                ['validator' => 'emailAddress'],
            ],
        ]);

        // Add the password element
        $this->addElement('password', 'password', [
            'label' => _tr('Password:'),
            'required' => true,
            'filters' => ['filter' => ['filter' => 'stringTrim']],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label' => _tr('Login'),
        ]);

        // Add referrer to redirect after login
        $this->addElement('hidden', 'referrer');
    }
}
