<?php

declare(strict_types=1);

namespace ApplicationTest\DBAL\Types;

use Application\DBAL\Types\ChronosType;
use Cake\Chronos\Chronos;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use PHPUnit\Framework\TestCase;

class ChronosTypeTest extends TestCase
{
    private ChronosType $type;

    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new ChronosType();
        $this->platform = new MySQLPlatform();
    }

    public function testConvertToDatabaseValue(): void
    {
        self::assertSame('DATETIME', $this->type->getSqlDeclaration(['foo'], $this->platform));

        $actual = $this->type->convertToDatabaseValue(new Chronos('2016-01-01 15:58:59'), $this->platform);
        self::assertSame('2016-01-01 15:58:59', $actual, 'support Chronos');

        $actual = $this->type->convertToDatabaseValue(new DateTimeImmutable('2016-01-01 15:58:59'), $this->platform);
        self::assertSame('2016-01-01 15:58:59', $actual, 'support DateTimeImmutable');

        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform), 'support null values');
    }

    public function testConvertToPHPValue(): void
    {
        $actualPhp = $this->type->convertToPHPValue('2016-01-01 15:58:59', $this->platform);
        self::assertInstanceOf(Chronos::class, $actualPhp);
        self::assertSame('2016-01-01 15:58:59', $actualPhp->__toString(), 'support string');

        $actualPhp = $this->type->convertToPHPValue(new Chronos('2016-01-01 15:58:59'), $this->platform);
        self::assertInstanceOf(Chronos::class, $actualPhp);
        self::assertSame('2016-01-01 15:58:59', $actualPhp->__toString(), 'support Chronos');

        $actualPhp = $this->type->convertToPHPValue(new DateTimeImmutable('2016-01-01 15:58:59'), $this->platform);
        self::assertInstanceOf(Chronos::class, $actualPhp);
        self::assertSame('2016-01-01 15:58:59', $actualPhp->__toString(), 'support DateTimeImmutable');

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
