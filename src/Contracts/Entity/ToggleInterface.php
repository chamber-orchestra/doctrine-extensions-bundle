<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity;

interface ToggleInterface
{
    public function isEnabled(): bool;

    public function toggle(): void;

    public function enable(): void;

    public function disable(): void;
}
