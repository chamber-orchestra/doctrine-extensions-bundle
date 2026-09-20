<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Repository;

use ChamberOrchestra\DoctrineExtensionsBundle\Exception\EntityNotFoundException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;

trait EntityRepositoryTrait
{
    /**
     * @param Criteria|array<string, mixed>|null $criteria
     * @param array<string, string|Order>|null   $orderBy
     *
     * @throws EntityNotFoundException
     */
    public function getOneBy(Criteria|array|null $criteria = null, ?array $orderBy = null): object
    {
        if ($criteria instanceof Criteria) {
            if (null !== $orderBy) {
                $criteria->orderBy(self::normalizeOrderings($orderBy));
            }
            $criteria->setMaxResults(1);
            $entity = $this->matching($criteria)->first();
        } else {
            $entity = $this->findOneBy($criteria ?? [], self::denormalizeOrderings($orderBy));
        }

        if (null === $entity || false === $entity) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }

    /**
     * @param array<string, mixed>  $criteria
     * @param array<string, string> $orderBy
     *
     * @return list<mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function indexBy(array $criteria = [], array $orderBy = [], string $field = 'id'): array
    {
        self::assertValidFieldName($field);

        $qb = $this->createQueryBuilder($alias = 'e');
        $qb->select($alias.'.'.$field);

        foreach ($criteria as $key => $value) {
            self::assertValidFieldName($key);

            if (null === $value) {
                $expr = $qb->expr()->isNull($alias.'.'.$key);
                $qb->andWhere($expr);
                continue;
            }

            if (\is_array($value)) {
                $expr = $qb->expr()->in($alias.'.'.$key, ':'.$key);
            } else {
                $expr = $qb->expr()->eq($alias.'.'.$key, ':'.$key);
            }
            $qb->andWhere($expr)->setParameter($key, $value);
        }

        foreach ($orderBy as $key => $value) {
            self::assertValidFieldName($key);
            self::assertValidOrderDirection($value);
            $qb->addOrderBy($alias.'.'.$key, $value);
        }

        return \array_column($qb->getQuery()->getArrayResult(), $field);
    }

    private static function assertValidFieldName(string $field): void
    {
        if (!\preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field)) {
            throw new \InvalidArgumentException(\sprintf('Invalid field name "%s".', $field));
        }
    }

    private static function assertValidOrderDirection(string $direction): void
    {
        if (!\in_array(\strtoupper($direction), ['ASC', 'DESC'], true)) {
            throw new \InvalidArgumentException(\sprintf('Invalid order direction "%s". Expected "ASC" or "DESC".', $direction));
        }
    }

    /**
     * Normalizes ordering directions for Criteria::orderBy().
     *
     * doctrine/collections 3.x requires the Order enum where 2.x accepted plain
     * strings, so string directions are still accepted here and converted.
     *
     * @param array<string, string|Order> $orderings
     *
     * @return array<string, Order>
     */
    private static function normalizeOrderings(array $orderings): array
    {
        return \array_map(
            static fn (string|Order $direction): Order => $direction instanceof Order
                ? $direction
                : Order::from(\strtoupper($direction)),
            $orderings,
        );
    }

    /**
     * Converts ordering directions back to the plain strings findOneBy() expects.
     *
     * @param array<string, string|Order>|null $orderings
     *
     * @return array<string, string>|null
     */
    private static function denormalizeOrderings(?array $orderings): ?array
    {
        if (null === $orderings) {
            return null;
        }

        return \array_map(
            static fn (string|Order $direction): string => $direction instanceof Order
                ? $direction->value
                : $direction,
            $orderings,
        );
    }
}
