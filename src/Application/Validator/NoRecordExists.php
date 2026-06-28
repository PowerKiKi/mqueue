<?php

namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

/**
 * Confirms a record does not exist in a table.
 */
class NoRecordExists extends AbstractValidator
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
     * @param "email"|"nickname" $field
     */
    public function __construct(
        private readonly string $field,
    )
    {
        parent::__construct();
    }

    /**
     * @param mixed $value
     */
    public function isValid($value): bool
    {
        $valid = true;
        $this->setValue($value);

        $result = $this->query($value);
        if ($result) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }

    private function query($value): bool
    {
        return (bool) _em()->getConnection()->fetchOne("SELECT COUNT(*) FROM user WHERE $this->field = :value", ['value' => $value]);
    }
}
