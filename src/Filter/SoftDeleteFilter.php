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
    private array $softDeleteCache = [];

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $class = $targetEntity->getName();

        if (($this->disabled[$class] ?? false) === true) {
            return '';
        }

        if (($this->disabled[$targetEntity->rootEntityName] ?? false) === true) {
            return '';
        }

        if (!($this->softDeleteCache[$class] ??= \is_a($class, SoftDeleteInterface::class, true))) {
            return '';
        }

        $column = $targetEntity->getColumnName(self::DELETED_DATETIME);

        return $targetTableAlias.'.'.$column.' IS NULL';
    }

    public function disableForEntity(string $class): void
    {
        $this->disabled[$class] = true;
    }

    public function enableForEntity(string $class): void
    {
        unset($this->disabled[$class]);
    }

    public function clearCache(): void
    {
        $this->softDeleteCache = [];
    }
}