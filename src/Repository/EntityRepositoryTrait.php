<?php
declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait EntityRepositoryTrait
{
    public function getOneBy(Criteria|array|null $criteria = null, ?array $orderBy = null): object
    {
        if ($criteria instanceof Criteria) {
            $criteria->orderBy($orderBy ?: [])->setMaxResults(1);
            $entity = $this->matching($criteria)->first();
        } else {
            $entity = $this->findOneBy($criteria, $orderBy);
        }

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    public function indexBy(array $criteria = [], array $orderBy = []): array
    {
        $qb = $this->createQueryBuilder($alias = 'n');
        $qb->select($alias.'.id');

        foreach ($criteria as $key => $value) {
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
            $qb->addOrderBy($alias.'.'.$key, $value);
        }

        return \array_column($qb->getQuery()->getArrayResult(), 'id');
    }
}