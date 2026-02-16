<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class ServiceEntityRepository extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function createQueryBuilder(string $alias, ?string $indexBy = null, bool $cacheable = false): QueryBuilder
    {
        $qb = parent::createQueryBuilder($alias, $indexBy);
        $qb->setCacheable($cacheable);

        return $qb;
    }
}
