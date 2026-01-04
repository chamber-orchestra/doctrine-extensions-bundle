<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineExtensionsBundle\Filter;

use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\SoftDeleteInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteFilter extends SQLFilter
{
    private const string DELETED_DATETIME = 'deletedDatetime';
    protected array $disabled = [];

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (\array_key_exists($class = $targetEntity->getName(), $this->disabled) && $this->disabled[$class] === true) {
            return '';
        }

        if (\array_key_exists($targetEntity->rootEntityName, $this->disabled) && $this->disabled[$targetEntity->rootEntityName] === true) {
            return '';
        }

        if (!\in_array(SoftDeleteInterface::class, \class_implements($class))) {
            return '';
        }

        $platform = $this->getConnection()->getDatabasePlatform();
        $column = $targetEntity->getColumnName(self::DELETED_DATETIME, $platform);

        return $targetTableAlias.'.'.$column.' IS NULL';
    }

    public function disableForEntity(string $class): void
    {
        $this->disabled[$class] = true;
    }

    public function enableForEntity(string $class): void
    {
        $this->disabled[$class] = false;
    }
}