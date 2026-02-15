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

trait EntityRepositoryTrait
{
    /**
     * @throws EntityNotFoundException
     */
    public function getOneBy(Criteria|array|null $criteria = null, ?array $orderBy = null): object
    {
        if ($criteria instanceof Criteria) {
            if ($orderBy !== null) {
                $criteria->orderBy($orderBy);
            }
            $criteria->setMaxResults(1);
            $entity = $this->matching($criteria)->first();
        } else {
            $entity = $this->findOneBy($criteria, $orderBy);
        }

        if (null === $entity || false === $entity) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }

    /**
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
}