<?php

class Default_Form_Import extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the comment element
        $this->addElement('text', 'url', array(
            'label'      => _tr('IMDb "Vote History" page url:'),
            'required'   => true,
            'validators' => array(
                array('validator' => 'Regex', 'options' => array("|mymovies/list\?l=\d+|"))
                )
        ));
        
        // Add the minimum for favorite
        $this->addElement('text', 'favoriteMinimum', array(
            'label'      => _tr('Minimum for favorite:'),
            'required'   => true,
            'validators' => array(
                array('validator' => 'Between', 'options' => array(0, 10)),
                array('validator' => 'Float', 'options' => array()),
                )
        ));
        
        // Add the minimum for excellent
        $this->addElement('text', 'excellentMinimum', array(
            'label'      => _tr('Minimum for excellent:'),
            'required'   => true,
            'validators' => array(
                array('validator' => 'Between', 'options' => array(0, 10)),
                array('validator' => 'Float', 'options' => array()),
                )
        ));
        
        // Add the minimum for favorite
        $this->addElement('text', 'okMinimum', array(
            'label'      => _tr('Minimum for ok:'),
            'required'   => true,
            'validators' => array(
                array('validator' => 'Between', 'options' => array(0, 10)),
                array('validator' => 'Float', 'options' => array()),
                )
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Add movie',
        ));
        
    }
}
