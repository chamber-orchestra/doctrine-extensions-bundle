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

trait ToggleTrait
{
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function toggle(): void
    {
        $this->enabled = !$this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }
}
