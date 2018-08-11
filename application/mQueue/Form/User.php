<?php

namespace mQueue\Form;

use Zend_Form;
use Zend_Validate_Db_NoRecordExists;

class User extends Zend_Form
{
    public function init(): void
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the nickname element
        $this->addElement('text', 'nickname', [
            'label' => _tr('Nickname:'),
            'autofocus' => true,
            'required' => true,
            'filters' => ['filter' => ['filter' => 'stringTrim']],
            'validators' => [
                ['validator' => new Zend_Validate_Db_NoRecordExists(['table' => 'user', 'field' => 'nickname'])],
            ],
        ]);

        // Add the email element
        $this->addElement('text', 'email', [
            'label' => _tr('Email:'),
            'required' => true,
            'filters' => ['filter' => ['filter' => 'stringTrim']],
            'validators' => [
                ['validator' => 'emailAddress'],
                ['validator' => new Zend_Validate_Db_NoRecordExists(['table' => 'user', 'field' => 'email'])],
            ],
        ]);

        // Add the password ail element
        $this->addElement('password', 'password', [
            'label' => _tr('Password:'),
            'required' => true,
        ]);

        // Add a captcha
        $this->addElement('captcha', 'captcha', [
            'label' => _tr('Are you a robot?'),
            'required' => true,
            'captcha' => 'figlet',
            'captchaOptions' => [
                'outputWidth' => 2000,
            ],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label' => _tr('Subscribe'),
        ]);

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', [
            'ignore' => true,
            'decorators' => ['ViewHelper'],
        ]);
    }
}
