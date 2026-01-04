<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Repository;

use ChamberOrchestra\DoctrineExtensionsBundle\Repository\EntityRepositoryTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EntityRepositoryTraitTest extends TestCase
{
    public function testGetOneByUsesMatchingForCriteria(): void
    {
        $entity = (object) ['id' => 1];

        $repo = new class($entity) {
            use EntityRepositoryTrait;

            public Criteria|null $matchedCriteria = null;

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
                return new \stdClass();
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
                return new \stdClass();
            }
        };

        $this->expectException(NotFoundHttpException::class);
        $repo->getOneBy(['id' => 1]);
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
        self::assertSame(['n.status IS NULL', 'n.type IN (:type)', 'n.owner = :owner'], $qb->whereClauses);
        self::assertSame(['id' => 'DESC'], $qb->orderBy);
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

    public function __construct(private readonly array $rows)
    {
    }

    public function select(string $select): self
    {
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
        if (str_starts_with($field, 'n.')) {
            $field = substr($field, 2);
        }
        $this->orderBy[$field] = $direction;

        return $this;
    }

    public function getQuery(): FakeQuery
    {
        return new FakeQuery($this->rows);
    }
}
