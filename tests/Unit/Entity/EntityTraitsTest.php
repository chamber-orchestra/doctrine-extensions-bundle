<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Entity;

use ChamberOrchestra\DoctrineExtensionsBundle\Entity\GeneratedIdTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\IdTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\SoftDeleteTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\ToggleTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\VersionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class EntityTraitsTest extends TestCase
{
    public function testIdTraitGetter(): void
    {
        $entity = new class {
            use IdTrait;

            public function setId(Uuid $id): void
            {
                $this->id = $id;
            }
        };

        $uuid = Uuid::v4();
        $entity->setId($uuid);

        self::assertSame($uuid, $entity->getId());
    }

    public function testGeneratedIdTraitGetter(): void
    {
        $entity = new class {
            use GeneratedIdTrait;

            public function setId(Uuid $id): void
            {
                $this->id = $id;
            }
        };

        $uuid = Uuid::v4();
        $entity->setId($uuid);

        self::assertSame($uuid, $entity->getId());
    }

    public function testToggleTraitToggles(): void
    {
        $entity = new class {
            use ToggleTrait;
        };

        self::assertTrue($entity->isEnabled());
        $entity->toggle();
        self::assertFalse($entity->isEnabled());
    }

    public function testSoftDeleteTraitMarksDeleted(): void
    {
        $entity = new class {
            use SoftDeleteTrait;
        };

        self::assertFalse($entity->isDeleted());
        $entity->delete();
        self::assertTrue($entity->isDeleted());
    }

    public function testVersionTraitDefinesProperty(): void
    {
        $entity = new class {
            use VersionTrait;
        };

        $property = new \ReflectionProperty($entity, 'version');
        self::assertTrue($property->isReadOnly());
    }
}
