<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Type;

use ChamberOrchestra\DoctrineExtensionsBundle\Exception\ExceptionInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Type\Exception\ConversionException;
use PHPUnit\Framework\TestCase;

final class ConversionExceptionTest extends TestCase
{
    public function testImplementsExceptionInterface(): void
    {
        $exception = ConversionException::conversionFailed('value', 'type');
        self::assertInstanceOf(ExceptionInterface::class, $exception);
    }

    public function testConversionFailedTruncatesLongValue(): void
    {
        $exception = ConversionException::conversionFailed(str_repeat('a', 40), 'custom');
        self::assertStringContainsString('Could not convert database value "aaaaaaaaaaaaaaaaaaaa..."', $exception->getMessage());
    }

    public function testConversionFailedFormatIncludesExpectedFormat(): void
    {
        $exception = ConversionException::conversionFailedFormat('bad', 'custom', 'Y-m-d');
        self::assertSame(
            'Could not convert database value "bad" to Doctrine Type custom. Expected format: Y-m-d',
            $exception->getMessage()
        );
    }

    public function testConversionFailedInvalidTypeIncludesExpectedTypes(): void
    {
        $exception = ConversionException::conversionFailedInvalidType('bad', 'custom', ['string', 'null']);
        self::assertSame(
            'Could not convert database value "bad" to Doctrine Type custom. Expected types: string, null',
            $exception->getMessage()
        );
    }
}
