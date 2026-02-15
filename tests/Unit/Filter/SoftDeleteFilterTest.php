<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Filter;

use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\SoftDeleteInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Filter\SoftDeleteFilter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

final class SoftDeleteFilterTest extends TestCase
{
    public function testAddsConstraintForSoftDeleteEntities(): void
    {
        $entityClass = new class implements SoftDeleteInterface {
            public function isDeleted(): bool
            {
                return false;
            }

            public function delete(): void
            {
            }

            public function restore(): void
            {
            }
        };

        $metadata = new ClassMetadata($entityClass::class);
        $metadata->rootEntityName = $entityClass::class;
        $metadata->mapField([
            'fieldName' => 'deletedDatetime',
            'columnName' => 'deleted_datetime',
            'type' => 'date_point',
        ]);

        $connection = $this->createStub(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new PostgreSQLPlatform());

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getConnection')->willReturn($connection);

        $filter = new SoftDeleteFilter($em);
        $sql = $filter->addFilterConstraint($metadata, 't');

        self::assertSame('t.deleted_datetime IS NULL', $sql);
    }

    public function testReturnsEmptyStringForNonSoftDeleteEntity(): void
    {
        $entityClass = new class {};

        $metadata = new ClassMetadata($entityClass::class);
        $metadata->rootEntityName = $entityClass::class;

        $em = $this->createStub(EntityManagerInterface::class);

        $filter = new SoftDeleteFilter($em);
        $sql = $filter->addFilterConstraint($metadata, 't');

        self::assertSame('', $sql);
    }

    public function testDisableForRootEntityNameBypassesFilter(): void
    {
        $entityClass = new class implements SoftDeleteInterface {
            public function isDeleted(): bool
            {
                return false;
            }

            public function delete(): void
            {
            }

            public function restore(): void
            {
            }
        };

        $metadata = new ClassMetadata($entityClass::class);
        $metadata->rootEntityName = 'App\\Entity\\BaseEntity';
        $metadata->mapField([
            'fieldName' => 'deletedDatetime',
            'columnName' => 'deleted_datetime',
            'type' => 'date_point',
        ]);

        $connection = $this->createStub(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new PostgreSQLPlatform());

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getConnection')->willReturn($connection);

        $filter = new SoftDeleteFilter($em);

        // Filter applies normally
        self::assertSame('t.deleted_datetime IS NULL', $filter->addFilterConstraint($metadata, 't'));

        // Disable via root entity name
        $filter->disableForEntity('App\\Entity\\BaseEntity');
        self::assertSame('', $filter->addFilterConstraint($metadata, 't'));

        // Re-enable via root entity name
        $filter->enableForEntity('App\\Entity\\BaseEntity');
        self::assertSame('t.deleted_datetime IS NULL', $filter->addFilterConstraint($metadata, 't'));
    }

    public function testDisableAndEnableForEntity(): void
    {
        $entityClass = new class implements SoftDeleteInterface {
            public function isDeleted(): bool
            {
                return false;
            }

            public function delete(): void
            {
            }

            public function restore(): void
            {
            }
        };

        $metadata = new ClassMetadata($entityClass::class);
        $metadata->rootEntityName = $entityClass::class;
        $metadata->mapField([
            'fieldName' => 'deletedDatetime',
            'columnName' => 'deleted_datetime',
            'type' => 'date_point',
        ]);

        $connection = $this->createStub(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new PostgreSQLPlatform());

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getConnection')->willReturn($connection);

        $filter = new SoftDeleteFilter($em);
        $filter->disableForEntity($entityClass::class);
        self::assertSame('', $filter->addFilterConstraint($metadata, 't'));

        $filter->enableForEntity($entityClass::class);
        self::assertSame('t.deleted_datetime IS NULL', $filter->addFilterConstraint($metadata, 't'));
    }

    public function testClearCacheResetsLookupResults(): void
    {
        $nonSoftDelete = new class {};

        $metadata = new ClassMetadata($nonSoftDelete::class);
        $metadata->rootEntityName = $nonSoftDelete::class;

        $em = $this->createStub(EntityManagerInterface::class);

        $filter = new SoftDeleteFilter($em);

        // First call caches that this class is NOT soft-deletable
        self::assertSame('', $filter->addFilterConstraint($metadata, 't'));

        // Clear cache so it can be re-evaluated
        $filter->clearCache();

        // Verify the filter still works correctly after cache clear
        self::assertSame('', $filter->addFilterConstraint($metadata, 't'));
    }
}
