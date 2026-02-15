<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Type;

use ChamberOrchestra\DoctrineExtensionsBundle\Type\DecimalType;
use ChamberOrchestra\DoctrineExtensionsBundle\Type\Exception\ConversionException;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;

final class DecimalTypeTest extends TestCase
{
    public function testDecimalTypeReturnsString(): void
    {
        $type = new DecimalType();
        $value = $type->convertToPHPValue('12.50', new PostgreSQLPlatform());
        self::assertSame('12.50', $value);
    }

    public function testDecimalTypeConvertsIntToString(): void
    {
        $type = new DecimalType();
        $value = $type->convertToPHPValue(42, new PostgreSQLPlatform());
        self::assertSame('42', $value);
    }

    public function testDecimalTypeConvertsFloatToString(): void
    {
        $type = new DecimalType();
        $value = $type->convertToPHPValue(3.14, new PostgreSQLPlatform());
        self::assertSame('3.14', $value);
    }

    public function testDecimalTypeReturnsNullForNullValue(): void
    {
        $type = new DecimalType();
        $value = $type->convertToPHPValue(null, new PostgreSQLPlatform());
        self::assertNull($value);
    }

    public function testDecimalTypeThrowsForNonScalarValue(): void
    {
        $type = new DecimalType();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue(new \stdClass(), new PostgreSQLPlatform());
    }

    public function testDecimalTypeRequiresSqlCommentHint(): void
    {
        $type = new DecimalType();
        self::assertTrue($type->requiresSQLCommentHint(new PostgreSQLPlatform()));
    }
}
