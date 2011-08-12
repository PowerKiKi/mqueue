<?php

class Default_Form_QuickSearch extends Zend_Form
{
    public function init()
    {
    	$this->setMethod('get');
		$this->setName('quickSearch');
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;

		$this->setAction($view->serverUrl() . $view->url(array('controller' => 'movie', 'action' => 'index'), 'default', false));

        // Add the comment element
        $this->addElement('text', 'search', array(
        	'placeholder' => _tr('search movie…'),
        	'decorators' => array('ViewHelper')
        ));

        // Add the submit button
        $this->addElement('submit', 'searchSubmit', array(
            'label'    => _tr('Search'),
        	'decorators' => array('ViewHelper'),
        ));


		$this->addDecorator('FormElements')
			->addDecorator('HtmlTag', array('tag' => 'div'))
			->addDecorator('Form');
    }
}
