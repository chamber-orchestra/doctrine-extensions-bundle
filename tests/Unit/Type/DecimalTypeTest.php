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

    public function testDecimalTypeRequiresSqlCommentHint(): void
    {
        $type = new DecimalType();
        self::assertTrue($type->requiresSQLCommentHint(new PostgreSQLPlatform()));
    }
}
