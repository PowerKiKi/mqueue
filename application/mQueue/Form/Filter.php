<?php

namespace mQueue\Form;

use mQueue\Model\Status;
use mQueue\Model\UserMapper;
use Zend_Form_SubForm;

class Filter extends Zend_Form_SubForm
{
    public function init(): void
    {
        // Set the method for the display form to GET
        $this->setMethod('get');

        $users = [];
        if (\mQueue\Model\User::getCurrent()) {
            $users = [0 => _tr('<< me >>')];
        }

        foreach (UserMapper::fetchAll() as $user) {
            $users[$user->id] = $user->nickname;
        }

        $this->addElement('select', 'user', [
            'multiOptions' => $users,
            'required' => true,
            'class' => 'filterUser',
            'validators' => [
                ['validator' => new Validate\User()],
            ],
            'filters' => [
                ['int'],
            ],
        ]);

        $this->addElement('select', 'condition', [
            'multiOptions' => ['is' => _tr('rating is'), 'isnot' => _tr('rating is not')],
            'required' => true,
        ]);

        $statuses = [];
        foreach (Status::$ratings as $rating => $label) {
            $statuses[$rating] = $this->getView()->rating($rating);
        }
        $statuses = $statuses + [0 => _tr('nothing')];

        $this->addElement('multiCheckbox', 'status', [
            'escape' => false,
            'multiOptions' => $statuses,
            'required' => true,
            'separator' => ' ',
            'filters' => [
                ['int'],
            ],
        ]);
        $this->status->getDecorator('HtmlTag')->setOption('class', 'filterStatus');

        // Add the title element
        $this->addElement('text', 'title', [
            'placeholder' => _tr('title'),
            'autofocus' => true,
            'filters' => [
                ['stringTrim'],
            ],
        ]);

        // Add the filter element
        $this->addElement('checkbox', 'withSource', [
            'label' => _tr('With source'),
        ]);
        $this->withSource->getDecorator('Label')->setOptions(['placement' => 'append']);

        $this->setDecorators([
            'FormElements',
            [['row' => 'HtmlTag'], ['tag' => 'dl', 'class' => 'filter']],
        ]);
    }

    /**
     * Disable extra field elements
     */
    public function disableExtraFields(): void
    {
        $this->removeElement('title');
        $this->removeElement('withSource');
    }

    /**
     * Override getValues() to replace special '0' value with current user
     *
     * @param bool $suppressArrayNotation
     *
     * @return array values
     */
    public function getValues($suppressArrayNotation = false)
    {
        $values = parent::getValues($suppressArrayNotation);

        if ($values['user'] == '0') {
            $values['user'] = \mQueue\Model\User::getCurrent()->id;
        }

        return $values;
    }

    /**
     * Returns values as readable text for end-user
     *
     * @return string
     */
    public function getValuesText()
    {
        $text = '';
        $values = $this->getValues(true);

        if (@$values['title']) {
            $text = _tr('title') . ':"' . $values['title'] . '" + ';
        }

        $users = $this->getElement('user')->getMultiOptions();
        $statuses = $this->getElement('status')->getMultiOptions();

        $statusLabels = [];
        foreach ($values['status'] as $status) {
            $statusLabels[] = Status::$ratings[$status] ?? $statuses[$status];
        }
        $text .= $users[$values['user']] . ':' . implode('+', $statusLabels);

        return $text;
    }
}
