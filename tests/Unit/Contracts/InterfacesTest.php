<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Contracts;

use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\GeneratedIdInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\IdInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\SoftDeleteInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\ToggleInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class InterfacesTest extends TestCase
{
    public function testIdInterfaceCanBeImplemented(): void
    {
        $entity = new class implements IdInterface {
            public function getId(): Uuid
            {
                return Uuid::v4();
            }
        };

        self::assertInstanceOf(IdInterface::class, $entity);
    }

    public function testGeneratedIdInterfaceCanBeImplemented(): void
    {
        $entity = new class implements GeneratedIdInterface {
            public function getId(): ?Uuid
            {
                return null;
            }
        };

        self::assertInstanceOf(GeneratedIdInterface::class, $entity);
    }

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

            public function restore(): void
            {
                $this->deleted = false;
            }

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
        };

        self::assertInstanceOf(SoftDeleteInterface::class, $entity);
        self::assertInstanceOf(ToggleInterface::class, $entity);
    }
}
