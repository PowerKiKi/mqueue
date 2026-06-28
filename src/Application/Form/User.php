<?php

namespace Application\Form;

use Application\Validator\NoRecordExists;
use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\Input;

class User extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('class', 'grid');

        // Add the nickname element
        $nickname = new Element\Text('nickname');
        $nickname->setLabel(_tr('Nickname:'));
        $nickname->setAttribute('required', true);
        $nickname->setAttribute('autofocus', true);
        $this->add($nickname);

        $nicknameInput = new Input('nickname');
        $nicknameInput->setRequired(true);
        $nicknameInput->getFilterChain()->attach(new StringTrim());
        $nicknameInput->getValidatorChain()->attach(new NoRecordExists('nickname'));

        // Add the email element
        $email = new Element\Email('email');
        $email->setLabel(_tr('Email:'));
        $email->setAttribute('required', true);
        $this->add($email);

        $emailInput = new Input('email');
        $emailInput->setRequired(true);
        $emailInput->getFilterChain()->attach(new StringTrim());
        $emailInput->getValidatorChain()->attach(new NoRecordExists('email'));

        // Add the password ail element
        $password = new Element\Password('password');
        $password->setLabel(_tr('Password:'));
        $password->setAttribute('required', true);
        $this->add($password);

        $passwordInput = new Input('password');
        $passwordInput->setRequired(true);

        // Add the submit button
        $submit = new Element\Submit('submit');
        $submit->setValue(_tr('Subscribe'));
        $this->add($submit);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($nicknameInput);
        $inputFilter->add($emailInput);
        $inputFilter->add($passwordInput);
    }
}
