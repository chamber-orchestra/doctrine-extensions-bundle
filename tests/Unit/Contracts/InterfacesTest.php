<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Contracts;

use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\SoftDeleteInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\ToggleInterface;
use PHPUnit\Framework\TestCase;

final class InterfacesTest extends TestCase
{
    public function testInterfacesCanBeImplemented(): void
    {
        $entity = new class implements SoftDeleteInterface, ToggleInterface {
            private bool $deleted = false;
            private bool $enabled = true;

            public function isDeleted(): bool
            {
                return $this->deleted;
            }

            public function delete(): void
            {
                $this->deleted = true;
            }

            public function isEnabled(): bool
            {
                return $this->enabled;
            }

            public function toggle(): void
            {
                $this->enabled = !$this->enabled;
            }
        };

        self::assertInstanceOf(SoftDeleteInterface::class, $entity);
        self::assertInstanceOf(ToggleInterface::class, $entity);
    }
}
