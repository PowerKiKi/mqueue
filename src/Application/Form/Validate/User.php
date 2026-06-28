<?php

namespace Application\Form\Validate;

use Laminas\Validator\AbstractValidator;

/**
 * Validator for User ID.
 */
class User extends AbstractValidator
{
    /**
     * Error constants.
     */
    public const string ERROR_RECORD_FOUND = 'recordFound';

    /**
     * @var array<string, string> Message templates
     */
    protected $messageTemplates = [
        self::ERROR_RECORD_FOUND => 'A record matching the input was found',
    ];

    /**
     * Returns true if and only if $value contains a valid User ID.
     *
     * @param int $value
     */
    public function isValid($value): bool
    {
        $valid = ($value === 0) || $this->query($value);
        if (!$valid) {
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }

    private function query($value): bool
    {
        return (bool) _em()->getConnection()->fetchOne('SELECT COUNT(*) FROM user WHERE id = :value', ['value' => $value]);
    }
}
