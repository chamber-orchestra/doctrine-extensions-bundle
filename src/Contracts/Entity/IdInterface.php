<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity;

use Symfony\Component\Uid\Uuid;

interface IdInterface
{
    public function getId(): Uuid;
}
