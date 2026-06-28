<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Cake\Chronos\ChronosDate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

final class DateType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    /**
     * @return ($value is null ? null : string)
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof ChronosDate) {
            return $value->format($platform->getDateFormatString());
        }

        throw InvalidType::new($value, self::class, ['null', ChronosDate::class]);
    }

    /**
     * @return ($value is null ? null : ChronosDate)
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ChronosDate
    {
        if ($value === null || $value instanceof ChronosDate) {
            return $value;
        }

        if (!is_string($value)) {
            throw InvalidFormat::new(
                (string) $value,
                self::class,
                $platform->getDateFormatString(),
            );
        }

        $val = new ChronosDate($value);

        return $val;
    }
}
