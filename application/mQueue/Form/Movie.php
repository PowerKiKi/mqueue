<?php

namespace mQueue\Form;

use Zend_Form;

class Movie extends Zend_Form
{
    public function init(): void
    {
        // Set the method for the display form to GET
        $this->setMethod('get');

        // Add the comment element
        $this->addElement('text', 'id', [
            'label' => _tr('IMDb url or id:'),
            'required' => true,
            'autofocus' => true,
            'validators' => [
                ['validator' => 'Regex', 'options' => ["/(\d{7,})/"]],
            ],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label' => _tr('Add movie'),
        ]);
    }
}
