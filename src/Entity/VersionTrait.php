<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\DatePointType;
use Symfony\Component\Clock\DatePoint;

trait VersionTrait
{
    #[ORM\Version]
    #[ORM\Column(type: DatePointType::NAME, scale: 6, nullable: false)]
    protected readonly DatePoint|null $version;
}