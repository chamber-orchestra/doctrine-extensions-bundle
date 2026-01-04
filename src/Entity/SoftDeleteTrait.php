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

trait SoftDeleteTrait
{
    #[ORM\Column(type: DatePointType::NAME, nullable: true)]
    protected DatePoint|null $deletedDatetime = null;

    public function isDeleted(): bool
    {
        return $this->deletedDatetime !== null;
    }

    public function delete(): void
    {
        $this->deletedDatetime = new DatePoint();
    }
}
