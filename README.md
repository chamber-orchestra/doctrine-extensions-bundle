# Chamber Orchestra Doctrine Extensions Bundle

A Symfony bundle that provides lightweight Doctrine extensions for common entity patterns and utility types.
It includes reusable entity traits, a soft-delete filter, a custom decimal DBAL type, and a SQL `random()` DQL function.

## Requirements

- PHP `^8.4`
- Symfony FrameworkBundle
- Doctrine ORM and DoctrineBundle

## Installation

Install via Composer:

```bash
composer require chamber-orchestra/doctrine-extensions-bundle
```

Enable the bundle (if not using Symfony Flex auto-discovery):

```php
// config/bundles.php
return [
    ChamberOrchestra\DoctrineExtensionsBundle\ChamberOrchestraDoctrineExtensionsBundle::class => ['all' => true],
];
```

## Usage

### Entity traits

Use the bundled traits in your Doctrine entities:

```php
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\IdTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\ToggleTrait;
use ChamberOrchestra\DoctrineExtensionsBundle\Entity\SoftDeleteTrait;

class Article
{
    use IdTrait;
    use ToggleTrait;
    use SoftDeleteTrait;
}
```

### Soft-delete filter

Register and enable the filter in Doctrine configuration, then disable per entity when needed:

```php
$filter = $entityManager->getFilters()->enable('soft_delete');
$filter->disableForEntity(Article::class);
```

### DQL random function

Register the function in Doctrine config and use it in DQL:

```php
// doctrine.yaml
// doctrine:
//   orm:
//     dql:
//       numeric_functions:
//         random: ChamberOrchestra\DoctrineExtensionsBundle\Function\Random
```

```php
$qb->select('a')->from(Article::class, 'a')->orderBy('random()');
```

### Decimal DBAL type

Use the custom type for decimal precision and ensure Doctrine knows the type:

```php
#[ORM\Column(type: 'decimal')]
private string $price;
```

## Dependencies

Declared in `composer.json`:

- `doctrine/orm`
- `doctrine/doctrine-bundle`
- `symfony/framework-bundle`

## Running tests

```bash
composer test
```

This executes PHPUnit using `phpunit.xml.dist`.
