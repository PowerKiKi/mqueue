<?php

namespace mQueue\Form;

use Zend_Form;

class Import extends Zend_Form
{
    public function init(): void
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the comment element
        $this->addElement('text', 'url', [
            'label' => _tr('IMDb "Vote History" page url:'),
            'autofocus' => true,
            'required' => true,
            'description' => _tr('eg: http://www.imdb.com/mymovies/list?l=39480251'),
            'validators' => [
                ['validator' => 'Regex', 'options' => ["|mymovies/list\?l=\d+|"]],
            ],
        ]);

        // Add the minimum for favorite
        $this->addElement('text', 'favoriteMinimum', [
            'label' => _tr('Minimum for favorite:'),
            'required' => true,
            'validators' => [
                ['validator' => 'Between', 'options' => [0, 10]],
                ['validator' => 'Float', 'options' => []],
            ],
        ]);

        // Add the minimum for excellent
        $this->addElement('text', 'excellentMinimum', [
            'label' => _tr('Minimum for excellent:'),
            'required' => true,
            'validators' => [
                ['validator' => 'Between', 'options' => [0, 10]],
                ['validator' => 'Float', 'options' => []],
            ],
        ]);

        // Add the minimum for favorite
        $this->addElement('text', 'okMinimum', [
            'label' => _tr('Minimum for ok:'),
            'required' => true,
            'validators' => [
                ['validator' => 'Between', 'options' => [0, 10]],
                ['validator' => 'Float', 'options' => []],
            ],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label' => 'Add movie',
        ]);
    }
}
