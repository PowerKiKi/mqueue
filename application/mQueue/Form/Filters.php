<?php

namespace mQueue\Form;

use Zend_Form;
use Zend_Controller_Action_HelperBroker;
use \mQueue\Model\UserMapper;

class Filters extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to GET
        $this->setMethod('get');
        $this->setName('filters');

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _tr('Apply'),
        ));

        // Add the submit button
        $this->addElement('submit', 'clear', array(
            'ignore' => true,
            'label' => _tr('Clear'),
        ));

        $this->addDecorator('Fieldset');

        $this->setDecorators(array(
            'FormElements',
            array(array('fieldset' => 'Fieldset'), array('legend' => 'Filter')),
            'Form',
        ));

        $this->addDisplayGroup(array('submit', 'clear'), 'filters', array('legend' => _tr('Filter')));

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;

        $this->addElement('image', 'addFilter', array(
            'src' => $view->serverUrl() . $view->baseUrl('/images/add.png'),
            'imageValue' => '1',
        ));
        $this->addDisplayGroup(array('addFilter'), 'addFilterGroup', array('class' => 'addFilter'));

        $this->setDisplayGroupDecorators(array(
            'FormElements',
            array(array('row' => 'HtmlTag'), array('tag' => 'dl', 'class' => 'buttons')),
        ));
    }

    /**
     * Overrides isValid to dynamically generate subforms which will be used for validation.
     * @param array $data
     */
    public function isValid($data)
    {
        $data = $this->createSubForms($data);

        return parent::isValid($data);
    }

    /**
     * Override setDefaults to dynamically generate subforms.
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $defaults = $this->createSubForms($defaults);

        // set defaults, which will propagate to newly created subforms
        return parent::setDefaults($defaults);
    }

    /**
     * Create actual filter as subforms according to filter values given.
     * It will at least create one subform. It may add a default subform
     * if 'addFilter_x' value is given.
     * @param array $defaults values of form
     * @return array $defaults modified values with additionnal filter
     */
    private function createSubForms(array $defaults)
    {
        // Find out the highest filter number
        $max = 0;
        foreach (array_keys($defaults) as $key) {
            if (preg_match('/^filter(\d+)$/', $key, $m)) {
                if ($m[1] > $max)
                    $max = $m[1];
            }
        }

        // If we specifically asked to add a filter or if there is none, then add a new filter with default value
        if ((isset($defaults['addFilter_x'])) || $max == 0) {
            $defaults['filter' . ($max + 1)] = array(
                'user' => \mQueue\Model\User::getCurrent() ? 0 : UserMapper::fetchAll()->current()->id,
                'status' => -1,
            );
        }

        // Create all filters
        $position = 1;
        foreach (array_keys($defaults) as $key) {
            if (preg_match('/^filter(\d+)$/', $key, $m)) {
                $subform = new Filter();
                if ($position > 1) {
                    $subform->disableExtraFields();
                }
                $this->addSubForm($subform, $key, $position++);
            }
        }

        return $defaults;
    }

    /**
     * Returns values as readable text for end-user
     * @return string
     */
    public function getValuesText()
    {
        $text = array();
        foreach ($this->getSubForms() as $subForm) {
            $text [] = $subForm->getValuesText();
        }

        return join(' + ', $text);
    }

}
