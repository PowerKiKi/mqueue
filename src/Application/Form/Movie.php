<?php

namespace Application\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\Input;
use Laminas\Validator\Regex;

class Movie extends Form
{
    protected $attributes = [
        'method' => 'GET',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('class', 'grid');

        // Add the ID element
        $id = new Element\Text('id');
        $id->setLabel(_tr('IMDb url or id:'));
        $id->setAttribute('required', true);
        $id->setAttribute('autofocus', true);
        $this->add($id);

        $idInput = new Input('id');
        $idInput->setRequired(true);
        $idInput->getValidatorChain()->attach(new Regex('/(\\d{7,})/'));

        // Add the submit button
        $submit = new Element\Submit('submit');
        $submit->setValue(_tr('Add movie'));
        $this->add($submit);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($idInput);
    }
}
