# Chamber Orchestra Doctrine Extensions Bundle

[![PHP Composer](https://github.com/chamber-orchestra/doctrine-extensions-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/doctrine-extensions-bundle/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/chamber-orchestra/doctrine-extensions-bundle/graph/badge.svg)](https://codecov.io/gh/chamber-orchestra/doctrine-extensions-bundle)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen)](https://phpstan.org/)
[![Latest Stable Version](https://poser.pugx.org/chamber-orchestra/doctrine-extensions-bundle/v)](https://packagist.org/packages/chamber-orchestra/doctrine-extensions-bundle)
[![License](https://poser.pugx.org/chamber-orchestra/doctrine-extensions-bundle/license)](https://packagist.org/packages/chamber-orchestra/doctrine-extensions-bundle)
![Symfony 8](https://img.shields.io/badge/Symfony-8-purple?logo=symfony)
[![Doctrine ORM](https://img.shields.io/badge/Doctrine%20ORM-3-FC6A31)](https://www.doctrine-project.org/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-only-336791)](https://www.postgresql.org/)

Lightweight Symfony bundle providing reusable Doctrine ORM extensions for PostgreSQL: entity traits with matching contract interfaces, a soft-delete SQL filter, extended repository base classes, a custom decimal DBAL type, and a `random()` DQL function.

## Requirements

- PHP ^8.5
- Symfony 8.0
- Doctrine ORM 3 / DoctrineBundle 3.2
- PostgreSQL

## Installation

```bash
composer require chamber-orchestra/doctrine-extensions-bundle
```

If you are not using Symfony Flex:

```php
// config/bundles.php
return [
    ChamberOrchestra\DoctrineExtensionsBundle\ChamberOrchestraDoctrineExtensionsBundle::class => ['all' => true],
];
```

## Features

### Entity Traits & Contract Interfaces

Each trait has a corresponding interface in `Contracts\Entity`. Implement the interface and use the trait:

```php
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\IdInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\SoftDeleteInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\ToggleInterface;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\IdTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\SoftDeleteTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\ToggleTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\VersionTrait;

class Article implements IdInterface, SoftDeleteInterface, ToggleInterface
{
    use IdTrait;
    use SoftDeleteTrait;
    use ToggleTrait;
    use VersionTrait;
}
```

| Trait | Interface | Fields & Methods |
|-------|-----------|-----------------|
| `IdTrait` | `IdInterface` | UUID primary key (caller-assigned). `getId(): Uuid` |
| `GeneratedIdTrait` | `GeneratedIdInterface` | UUID primary key (auto-generated, nullable before persist). `getId(): ?Uuid` |
| `SoftDeleteTrait` | `SoftDeleteInterface` | `deletedDatetime` column. `isDeleted()`, `delete()`, `restore()` |
| `ToggleTrait` | `ToggleInterface` | `enabled` boolean column. `isEnabled()`, `toggle()`, `enable()`, `disable()` |
| `VersionTrait` | — | Doctrine `@Version` column using `DatePoint` (microsecond precision). `getVersion()` |

### Soft-Delete Filter

Automatically appends `deleted_datetime IS NULL` to queries for entities implementing `SoftDeleteInterface`. Bypass per entity when needed:

```php
$filter = $entityManager->getFilters()->enable('soft_delete');
$filter->disableForEntity(Article::class);   // include soft-deleted articles
$filter->enableForEntity(Article::class);    // re-enable filtering
```

### Repository Base Classes

Two base classes provide `getOneBy()` and `indexBy()` out of the box:

- `ServiceEntityRepository` — extends Doctrine bundle's `ServiceEntityRepository`, adds `$cacheable` parameter to `createQueryBuilder()`
- `EntityRepository` — extends Doctrine ORM's `EntityRepository`, implements `ServiceEntityRepositoryInterface`

```php
use ChamberOrchestra\DoctrineExtensionsBundle\Repository\ServiceEntityRepository;

class ArticleRepository extends ServiceEntityRepository
{
    // getOneBy(criteria, orderBy) — throws EntityNotFoundException if not found
    // indexBy(criteria, orderBy, field) — returns array of field values matching criteria
}
```

### Custom Decimal DBAL Type

`DecimalType` overrides Doctrine's `DecimalType` to ensure `convertToPHPValue()` always returns `?string` with scalar type validation.

### DQL Random Function (PostgreSQL)

Maps `random()` DQL to PostgreSQL `random()`:

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        dql:
            numeric_functions:
                random: ChamberOrchestra\DoctrineExtensionsBundle\Function\Random
```

```php
$qb->select('a')->from(Article::class, 'a')->orderBy('random()');
```

## Testing

```bash
composer test
```

## License

MIT
