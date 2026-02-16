<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integrational\Entity;

use ChamberOrchestra\DoctrineExtensionsBundle\Entity\VersionTrait;
use Doctrine\ORM\Mapping as ORM;
use ReflectionProperty;
use Symfony\Bridge\Doctrine\Types\DatePointType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\DatePoint;
use Tests\Integrational\TestKernel;

final class VersionTraitTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testVersionPropertyHasCorrectOrmAttributes(): void
    {
        self::bootKernel();

        $entity = new class {
            use VersionTrait;
        };

        $ref = new ReflectionProperty($entity, 'version');

        $versionAttrs = $ref->getAttributes(ORM\Version::class);
        self::assertCount(1, $versionAttrs, 'Version property must have #[ORM\Version] attribute');

        $columnAttrs = $ref->getAttributes(ORM\Column::class);
        self::assertCount(1, $columnAttrs, 'Version property must have #[ORM\Column] attribute');

        $column = $columnAttrs[0]->newInstance();
        self::assertSame(DatePointType::NAME, $column->type);
        self::assertSame(6, $column->scale);
        self::assertFalse($column->nullable);
    }

    public function testVersionPropertyType(): void
    {
        self::bootKernel();

        $entity = new class {
            use VersionTrait;
        };

        $ref = new ReflectionProperty($entity, 'version');
        $type = $ref->getType();

        self::assertNotNull($type);
        self::assertSame(DatePoint::class, $type->getName());
        self::assertFalse($type->allowsNull());
    }

    public function testGetVersionReturnsAssignedValue(): void
    {
        self::bootKernel();

        $entity = new class {
            use VersionTrait;

            public function setVersion(DatePoint $v): void
            {
                $this->version = $v;
            }
        };

        $version = new DatePoint();
        $entity->setVersion($version);

        self::assertSame($version, $entity->getVersion());
    }
}
