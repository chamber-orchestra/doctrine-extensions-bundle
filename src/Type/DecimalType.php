<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class DecimalType extends \Doctrine\DBAL\Types\DecimalType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        $val = parent::convertToPHPValue($value, $platform);

        return null !== $val ? (string)$val : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}