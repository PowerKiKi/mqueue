<?php

namespace Application\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\Input;

class Login extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('class', 'grid');

        // Add the email
        $email = new Element\Email('email');
        $email->setLabel(_tr('Email:'));
        $email->setAttribute('required', true);
        $email->setAttribute('autofocus', true);
        $this->add($email);

        // Add the password
        $password = new Element\Password('password');
        $password->setLabel(_tr('Password:'));
        $password->setAttribute('required', true);
        $this->add($password);

        $passwordInput = new Input('password');
        $passwordInput->setRequired(true);

        // Add the submit button
        $submit = new Element\Submit('submit');
        $submit->setValue(_tr('Login'));
        $this->add($submit);

        // Add referrer to redirect after login
        $referrer = new Element\Hidden('referrer');
        $this->add($referrer);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($passwordInput);
    }
}
