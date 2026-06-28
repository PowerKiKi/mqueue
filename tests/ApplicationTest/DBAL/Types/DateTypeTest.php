<?php

declare(strict_types=1);

namespace ApplicationTest\DBAL\Types;

use Application\DBAL\Types\DateType;
use Cake\Chronos\ChronosDate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use PHPUnit\Framework\TestCase;

class DateTypeTest extends TestCase
{
    private DateType $type;

    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new DateType();
        $this->platform = new MySQLPlatform();
    }

    public function testConvertToDatabaseValue(): void
    {
        self::assertSame('DATE', $this->type->getSqlDeclaration(['foo'], $this->platform));

        $actual = $this->type->convertToDatabaseValue(new ChronosDate('2016-01-01'), $this->platform);
        self::assertSame('2016-01-01', $actual, 'support Chronos');

        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform), 'support null values');
    }

    public function testConvertToPHPValue(): void
    {
        $actualPhp = $this->type->convertToPHPValue('2022-12-31', $this->platform);
        self::assertInstanceOf(ChronosDate::class, $actualPhp);
        self::assertSame('2022-12-31', $actualPhp->__toString(), 'support string');

        $actualPhp = $this->type->convertToPHPValue(new ChronosDate('2022-12-31'), $this->platform);
        self::assertInstanceOf(ChronosDate::class, $actualPhp);
        self::assertSame('2022-12-31', $actualPhp->__toString(), 'support ChronosDate');

        self::assertNull($this->type->convertToPHPValue(null, $this->platform), 'support null values');
    }

    public function testConvertToPHPValueThrowsWithInvalidValue(): void
    {
        $this->expectException(InvalidFormat::class);

        $this->type->convertToPHPValue(123, $this->platform);
    }

    public function testConvertToDatabaseValueThrowsWithInvalidValue(): void
    {
        $this->expectException(InvalidType::class);

        $this->type->convertToDatabaseValue(123, $this->platform);
    }
}
