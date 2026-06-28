<?php

namespace Application\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;

class QuickSearch extends Form
{
    protected $attributes = [
        'method' => 'GET',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setName('quickSearch');

        $this->setAttribute('action', '/movie');

        // Add the comment element
        $search = new Element\Text('search');
        $search->setAttribute('placeholder', _tr('search movie…'));
        $this->add($search);

        // Add the submit button
        $submit = new Element\Submit('searchSubmit');
        $submit->setValue(_tr('Search'));
        $this->add($submit);
    }
}
