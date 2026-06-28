<?php

namespace Application\Form;

use Application\Enum\Rating;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;

class Filters extends Form
{
    protected $attributes = [
        'method' => 'GET',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setName('filters');

        // Add the submit button
        $submit = new Element\Submit('submit');
        $submit->setValue(_tr('Apply'));
        $this->add($submit);

        $image = new Element\Image('addFilter');
        $image->setValue(_tr('Apply'));
        $image->setAttribute('class', 'addFilter');
        $image->setAttribute('src', '/images/add.png');
        $this->add($image);
    }

    /**
     * Overrides isValid to dynamically generate subforms which will be used for validation.
     */
    public function setData(?iterable $data): self
    {
        $data = $this->createSubForms($data);

        parent::setData($data);

        return $this;
    }

    /**
     * Override getValues() to replace special '0' value with current user.
     */
    public function getData(int $flag = FormInterface::VALUES_NORMALIZED): array
    {
        $data = parent::getData($flag) ?? [];
        foreach ($data as $key => &$value) {
            if (!$this->isFilter($key)) {
                continue;
            }

            if ($value['user'] === 0) {
                $value['user'] = \Application\Model\User::getCurrent()?->id;

            }
        }

        return $data;
    }

    /**
     * Create actual filter as subforms according to filter values given.
     * It will at least create one subform. It may add a default subform
     * if 'addFilter_x' value is given.
     *
     * @param array $defaults values of form
     *
     * @return array $defaults modified values with additional filter
     */
    private function createSubForms(iterable $defaults): array
    {
        // Find out the highest filter number
        $max = 0;
        foreach (array_keys($defaults) as $key) {
            if (preg_match('/^filter(\d+)$/', $key, $m)) {
                if ($m[1] > $max) {
                    $max = $m[1];
                }
            }
        }

        // If we specifically asked to add a filter or if there is none, then add a new filter with default value
        if (isset($defaults['addFilter_x']) || $max === 0) {

            $defaults['filter' . ($max + 1)] = [
                'user' => \Application\Model\User::getCurrent() ? 0 : _em()->getRepository(\Application\Model\User::class)->findOneBy([])->id,
                'condition' => 'is',
                'status' => Rating::possibleValues(),
                'withSource' => false,
            ];
        }

        // Create all filters
        $first = true;
        foreach ($defaults as $key => &$value) {
            if ($this->isFilter($key)) {
                $subform = new Filter($key, $first);
                $this->add($subform, ['priority' => 1]);
                $first = false;

                // No status choice, means "nothing" choice
                $status = $value['status'] ?? [];
                if (!$status) {
                    $value['status'] = [0];
                }
            }
        }

        return $defaults;
    }

    /**
     * Returns values as readable text for end-user.
     */
    public function getValuesText(): string
    {
        $data = $this->getData();
        $text = [];
        foreach ($data as $key => $value) {
            if (!$this->isFilter($key)) {
                continue;
            }
            $text[] = $this->getValuesTextOneFilter($value);
        }

        return implode(' + ', $text);
    }

    private function getValuesTextOneFilter(array $values): string
    {
        $text = '';

        if (@$values['title']) {
            $text = _tr('title') . ':"' . $values['title'] . '" + ';
        }

        $id = $values['user'];
        if (!$id) {
            return '';
        }
        $user = _em()->getRepository(\Application\Model\User::class)->findOneById($id);

        $statusLabels = [];
        foreach ($values['status'] as $status) {
            $statusLabels[] = Rating::from($status)->name();
        }
        $text .= $user->nickname . ':' . implode('+', $statusLabels);

        return $text;
    }

    private function isFilter(string $key): bool
    {
        return preg_match('~^filter\d+$~', $key);
    }
}
