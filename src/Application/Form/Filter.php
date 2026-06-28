<?php

namespace Application\Form;

use Application\Enum\Rating;
use Laminas\Filter\StringTrim;
use Laminas\Filter\ToInt;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilterProviderInterface;

class Filter extends Fieldset implements InputFilterProviderInterface
{
    protected $attributes = [
        'method' => 'GET',
    ];

    public function __construct(
        string $name,
        private readonly bool $withExtraField,
    )
    {
        parent::__construct($name);

        $users = [];
        if (\Application\Model\User::getCurrent()) {
            $users = [0 => _tr('<< me >>')];
        }

        foreach (_em()->getRepository(\Application\Model\User::class)->getAll() as $user) {
            $users[$user->id] = $user->nickname;
        }

        // Add the user element
        $user = new Element\Select('user');
        $user->setAttribute('required', true);
        $user->setAttribute('class', 'filterUser');
        $user->setValueOptions($users);
        $this->add($user);

        // Add the condition element
        $condition = new Element\Select('condition');
        $condition->setAttribute('required', true);
        $condition->setValueOptions([
            'is' => _tr('rating is'),
            'isnot' => _tr('rating is not'),
        ]);
        $this->add($condition);

        $statuses = [];
        $helper = new \Application\View\Helper\Rating();
        foreach (Rating::possibleChoices() as $rating) {
            $statuses[$rating->value] = $helper($rating);
        }
        $statuses = $statuses + [Rating::Nothing->value => _tr('nothing')];

        // Add the status element
        $status = new Element\MultiCheckbox('status');
        $status->setAttribute('class', 'filterStatus');
        $status->setValueOptions($statuses);
        $status->setLabelOption('disable_html_escape', true);
        $this->add($status);

        if ($this->withExtraField) {
            // Add the title element
            $title = new Element\Text('title');
            $title->setAttribute('autofocus', true);
            $title->setAttribute('placeholder', _tr('title'));
            $this->add($title);

            // Add the withSource element
            $withSource = new Element\Checkbox('withSource');
            $withSource->setAttribute('autofocus', true);
            $withSource->setAttribute('class', 'inversed-checkbox');
            $withSource->setLabel(_tr('With source'));
            $this->add($withSource);
        }
    }

    public function getInputFilterSpecification(): array
    {
        $userInput = new Input('user');
        $userInput->setRequired(true);
        $userInput->getFilterChain()->attach(new ToInt());
        $userInput->getValidatorChain()->attach(new Validate\User());

        $conditionInput = new Input('condition');
        $conditionInput->setRequired(true);

        $statusInput = new Input('status');
        $statusInput->getFilterChain()->attach(new ToInt());

        $result = [
            $userInput,
            $conditionInput,
            $statusInput,
        ];

        if ($this->withExtraField) {
            $titleInput = new Input('title');
            $titleInput->getFilterChain()->attach(new StringTrim());
            $titleInput->setRequired(false);
            $titleInput->setAllowEmpty(true);

            $withSourceInput = new Input('withSource');
            $withSourceInput->setAllowEmpty(true);

            $result[] = $titleInput;
            $result[] = $withSourceInput;
        }

        return $result;
    }
}
