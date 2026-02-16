<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Repository;

use ChamberOrchestra\DoctrineExtensionsBundle\Exception\EntityNotFoundException;
use ChamberOrchestra\DoctrineExtensionsBundle\Repository\EntityRepositoryTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class EntityRepositoryTraitTest extends TestCase
{
    public function testGetOneByUsesMatchingForCriteria(): void
    {
        $entity = (object) ['id' => 1];

        $repo = new class($entity) {
            use EntityRepositoryTrait;

            public ?Criteria $matchedCriteria = null;

            public function __construct(private readonly object $entity)
            {
            }

            public function matching(Criteria $criteria): ArrayCollection
            {
                $this->matchedCriteria = $criteria;

                return new ArrayCollection([$this->entity]);
            }

            public function findOneBy(?array $criteria = null, ?array $orderBy = null): ?object
            {
                return null;
            }

            public function createQueryBuilder(string $alias): object
            {
                return new stdClass();
            }
        };

        $criteria = Criteria::create(true);
        $result = $repo->getOneBy($criteria, ['id' => Order::Ascending]);

        self::assertSame($entity, $result);
        self::assertSame(['id' => Order::Ascending->value], $criteria->getOrderings());
        self::assertSame(1, $criteria->getMaxResults());
        self::assertSame($criteria, $repo->matchedCriteria);
    }

    public function testGetOneByThrowsWhenMissing(): void
    {
        $repo = new class {
            use EntityRepositoryTrait;

            public function matching(Criteria $criteria): ArrayCollection
            {
                return new ArrayCollection([]);
            }

            public function findOneBy(?array $criteria = null, ?array $orderBy = null): ?object
            {
                return null;
            }

            public function createQueryBuilder(string $alias): object
            {
                return new stdClass();
            }
        };

        $this->expectException(EntityNotFoundException::class);
        $repo->getOneBy(['id' => 1]);
    }

    public function testGetOneByReturnsEntityWithArrayCriteria(): void
    {
        $entity = (object) ['id' => 1];

        $repo = new class($entity) {
            use EntityRepositoryTrait;

            public function __construct(private readonly object $entity)
            {
            }

            public function matching(Criteria $criteria): ArrayCollection
            {
                return new ArrayCollection([]);
            }

            public function findOneBy(?array $criteria = null, ?array $orderBy = null): ?object
            {
                return $this->entity;
            }

            public function createQueryBuilder(string $alias): object
            {
                return new stdClass();
            }
        };

        $result = $repo->getOneBy(['id' => 1]);
        self::assertSame($entity, $result);
    }

    public function testGetOneByWithCriteriaPreservesExistingOrdering(): void
    {
        $entity = (object) ['id' => 1];

        $repo = new class($entity) {
            use EntityRepositoryTrait;

            public ?Criteria $matchedCriteria = null;

            public function __construct(private readonly object $entity)
            {
            }

            public function matching(Criteria $criteria): ArrayCollection
            {
                $this->matchedCriteria = $criteria;

                return new ArrayCollection([$this->entity]);
            }

            public function findOneBy(?array $criteria = null, ?array $orderBy = null): ?object
            {
                return null;
            }

            public function createQueryBuilder(string $alias): object
            {
                return new stdClass();
            }
        };

        $criteria = Criteria::create(true);
        $criteria->orderBy(['name' => Order::Descending]);
        $result = $repo->getOneBy($criteria);

        self::assertSame($entity, $result);
        self::assertSame(['name' => Order::Descending->value], $criteria->getOrderings());
        self::assertSame(1, $criteria->getMaxResults());
    }

    public function testIndexByBuildsQueryAndReturnsIds(): void
    {
        $qb = new FakeQueryBuilder([
            ['id' => 1],
            ['id' => 2],
        ]);

        $repo = new class($qb) {
            use EntityRepositoryTrait;

            public function __construct(private readonly FakeQueryBuilder $qb)
            {
            }

            public function createQueryBuilder(string $alias): FakeQueryBuilder
            {
                return $this->qb;
            }
        };

        $result = $repo->indexBy(['status' => null, 'type' => ['a', 'b'], 'owner' => 1], ['id' => 'DESC']);

        self::assertSame([1, 2], $result);
        self::assertSame(['type' => ['a', 'b'], 'owner' => 1], $qb->parameters);
        self::assertSame(['e.status IS NULL', 'e.type IN (:type)', 'e.owner = :owner'], $qb->whereClauses);
        self::assertSame(['id' => 'DESC'], $qb->orderBy);
    }

    public function testIndexByWithCustomField(): void
    {
        $qb = new FakeQueryBuilder([
            ['uuid' => 'a'],
            ['uuid' => 'b'],
        ]);

        $repo = new class($qb) {
            use EntityRepositoryTrait;

            public function __construct(private readonly FakeQueryBuilder $qb)
            {
            }

            public function createQueryBuilder(string $alias): FakeQueryBuilder
            {
                return $this->qb;
            }
        };

        $result = $repo->indexBy(field: 'uuid');

        self::assertSame(['a', 'b'], $result);
        self::assertSame('e.uuid', $qb->selectedField);
    }

    public function testIndexByRejectsInvalidCriteriaFieldName(): void
    {
        $qb = new FakeQueryBuilder([]);

        $repo = new class($qb) {
            use EntityRepositoryTrait;

            public function __construct(private readonly FakeQueryBuilder $qb)
            {
            }

            public function createQueryBuilder(string $alias): FakeQueryBuilder
            {
                return $this->qb;
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field name');
        $repo->indexBy(['id; DROP TABLE users' => 1]);
    }

    public function testIndexByRejectsInvalidOrderByFieldName(): void
    {
        $qb = new FakeQueryBuilder([]);

        $repo = new class($qb) {
            use EntityRepositoryTrait;

            public function __construct(private readonly FakeQueryBuilder $qb)
            {
            }

            public function createQueryBuilder(string $alias): FakeQueryBuilder
            {
                return $this->qb;
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field name');
        $repo->indexBy([], ['id OR 1=1' => 'ASC']);
    }

    public function testIndexByRejectsInvalidOrderDirection(): void
    {
        $qb = new FakeQueryBuilder([]);

        $repo = new class($qb) {
            use EntityRepositoryTrait;

            public function __construct(private readonly FakeQueryBuilder $qb)
            {
            }

            public function createQueryBuilder(string $alias): FakeQueryBuilder
            {
                return $this->qb;
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid order direction');
        $repo->indexBy([], ['id' => 'INVALID']);
    }

    public function testGetOneByThrowsWithDefaultMessage(): void
    {
        $repo = new class {
            use EntityRepositoryTrait;

            public function matching(Criteria $criteria): ArrayCollection
            {
                return new ArrayCollection([]);
            }

            public function findOneBy(?array $criteria = null, ?array $orderBy = null): ?object
            {
                return null;
            }

            public function createQueryBuilder(string $alias): object
            {
                return new stdClass();
            }
        };

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity not found.');
        $repo->getOneBy(['id' => 1]);
    }
}

final class FakeExpr
{
    public function isNull(string $field): string
    {
        return $field.' IS NULL';
    }

    public function in(string $field, string $param): string
    {
        return $field.' IN ('.$param.')';
    }

    public function eq(string $field, string $param): string
    {
        return $field.' = '.$param;
    }
}

final class FakeQuery
{
    public function __construct(private readonly array $rows)
    {
    }

    public function getArrayResult(): array
    {
        return $this->rows;
    }
}

final class FakeQueryBuilder
{
    public array $parameters = [];
    public array $whereClauses = [];
    public array $orderBy = [];
    public string $selectedField = '';

    public function __construct(private readonly array $rows)
    {
    }

    public function select(string $select): self
    {
        $this->selectedField = $select;

        return $this;
    }

    public function expr(): FakeExpr
    {
        return new FakeExpr();
    }

    public function andWhere(string $expr): self
    {
        $this->whereClauses[] = $expr;

        return $this;
    }

    public function setParameter(string $key, mixed $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function addOrderBy(string $field, string $direction): self
    {
        if (\str_starts_with($field, 'e.')) {
            $field = \substr($field, 2);
        }
        $this->orderBy[$field] = $direction;

        return $this;
    }

    public function getQuery(): FakeQuery
    {
        return new FakeQuery($this->rows);
    }
}
