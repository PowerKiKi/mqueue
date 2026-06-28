<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Cake\Chronos\Chronos;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

final class ChronosType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($column);
    }

    /**
     * @return ($value is null ? null : string)
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format($platform->getDateTimeFormatString());
        }

        throw InvalidType::new($value, self::class, ['null', Chronos::class]);
    }

    /**
     * @return ($value is null ? null : Chronos)
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Chronos
    {
        if ($value === null || $value instanceof Chronos) {
            return $value;
        }

        if (!is_string($value) && !$value instanceof DateTimeInterface) {
            throw InvalidFormat::new(
                (string) $value,
                self::class,
                $platform->getDateTimeFormatString(),
            );
        }

        $val = new Chronos($value);

        return $val;
    }
}
