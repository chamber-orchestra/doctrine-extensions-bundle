<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Repository;

use ChamberOrchestra\DoctrineExtensionsBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class ServiceEntityRepositoryTest extends TestCase
{
    public function testCreateQueryBuilderSetsCacheableFlag(): void
    {
        $entityClass = DummyEntity::class;
        $metadata = new ClassMetadata($entityClass);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('setCacheable')->with(true)->willReturnSelf();

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('createQueryBuilder')->willReturn($queryBuilder);
        $em->method('getClassMetadata')->with($entityClass)->willReturn($metadata);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->with($entityClass)->willReturn($em);

        $repository = new ServiceEntityRepository($registry, $entityClass);

        $result = $repository->createQueryBuilder('e', null, true);

        self::assertSame($queryBuilder, $result);
    }
}

final class DummyEntity
{
}
