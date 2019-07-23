<?php

namespace mQueue\Form;

use Zend_Controller_Action_HelperBroker;
use Zend_Form;

class QuickSearch extends Zend_Form
{
    public function init(): void
    {
        $this->setMethod('get');
        $this->setName('quickSearch');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;

        $this->setAction($view->serverUrl() . $view->url(['controller' => 'movie', 'action' => 'index'], 'default', false));

        // Add the comment element
        $this->addElement('text', 'search', [
            'placeholder' => _tr('search movie…'),
            'decorators' => ['ViewHelper'],
        ]);

        // Add the submit button
        $this->addElement('submit', 'searchSubmit', [
            'label' => _tr('Search'),
            'decorators' => ['ViewHelper'],
        ]);

        $this->addDecorator('FormElements')
            ->addDecorator('HtmlTag', ['tag' => 'span'])
            ->addDecorator('Form');
    }
}
