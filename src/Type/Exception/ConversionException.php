<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Type\Exception;

use ChamberOrchestra\DoctrineExtensionsBundle\Exception\ExceptionInterface;

class ConversionException extends \Doctrine\DBAL\Types\ConversionException implements ExceptionInterface
{
    private const int MAX_DISPLAY_LENGTH = 32;
    private const int TRUNCATED_LENGTH = 20;

    public static function conversionFailed(string $value, string $toType): self
    {
        return new self('Could not convert database value "'.self::truncate($value).'" to Doctrine Type '.$toType);
    }

    public static function conversionFailedFormat(string $value, string $toType, string $expectedFormat): self
    {
        return new self(
            'Could not convert database value "'.self::truncate($value).'" to Doctrine Type '.
            $toType.'. Expected format: '.$expectedFormat
        );
    }

    public static function conversionFailedInvalidType(string $value, string $toType, array $expectedTypes = []): self
    {
        return new self(
            'Could not convert database value "'.self::truncate($value).'" to Doctrine Type '.
            $toType.'. Expected types: '.\implode(', ', $expectedTypes)
        );
    }

    private static function truncate(string $value): string
    {
        return (\strlen($value) > self::MAX_DISPLAY_LENGTH) ? \substr($value, 0, self::TRUNCATED_LENGTH).'...' : $value;
    }
}
