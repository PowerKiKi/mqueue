<?php

class Default_Form_Movie extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add the comment element
        $this->addElement('text', 'id', array(
            'label'      => 'IMDb url or id:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'Regex', 'options' => array("/(\d{7})/"))
                )
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Add movie',
        ));

        // And finally add some CSRF protection
      //  $this->addElement('hash', 'csrf', array(
     //       'ignore' => true,
     //   ));
    }
}
