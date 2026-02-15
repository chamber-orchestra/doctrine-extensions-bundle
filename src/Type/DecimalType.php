<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Type;

use ChamberOrchestra\DoctrineExtensionsBundle\Type\Exception\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DecimalType extends \Doctrine\DBAL\Types\DecimalType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!\is_scalar($value)) {
            throw ConversionException::conversionFailedInvalidType(\get_debug_type($value), 'decimal', ['string', 'int', 'float']);
        }

        return (string) $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}