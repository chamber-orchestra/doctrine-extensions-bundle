# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Symfony bundle (`chamber-orchestra/doctrine-extensions-bundle`) that provides lightweight Doctrine ORM extensions for common entity patterns **for PostgreSQL only**. It includes reusable entity traits with matching contract interfaces, a soft-delete SQL filter, a custom decimal DBAL type, extended repository base classes, and a `random()` DQL function. SQL dialect features (e.g. `random()`) target PostgreSQL and are not tested against other databases.

## Commands

```sh
composer install                       # Install dependencies
composer test                          # Run full PHPUnit suite (alias for bin/phpunit)
vendor/bin/phpunit                     # Run full test suite directly
vendor/bin/phpunit --filter SomeTest   # Run specific test class/method
```

## Architecture

### Entity traits + contracts pattern

Each trait in `src/Entity/` has a corresponding interface in `src/Contracts/Entity/`. Entities should implement the interface and use the trait:

- **`IdTrait`** — UUID primary key via `Symfony\Component\Uid\Uuid` (caller-assigned)
- **`GeneratedIdTrait`** — UUID primary key with `UuidGenerator` (auto-generated, nullable before persist)
- **`ToggleTrait` / `ToggleInterface`** — boolean `enabled` field with `isEnabled()` and `toggle()`
- **`SoftDeleteTrait` / `SoftDeleteInterface`** — `deletedDatetime` field using Symfony `DatePoint`; `isDeleted()` and `delete()`
- **`VersionTrait`** — Doctrine `@Version` column using `DatePoint` with microsecond precision

### Soft-delete filter

`SoftDeleteFilter` extends Doctrine's `SQLFilter`. It automatically adds `deleted_datetime IS NULL` to queries for any entity implementing `SoftDeleteInterface`. Per-entity bypass via `disableForEntity()` / `enableForEntity()`.

### Repository layer

`EntityRepositoryTrait` provides `getOneBy()` (throws `NotFoundHttpException` if not found, supports both array criteria and `Criteria` objects) and `indexBy()` (returns array of IDs matching criteria). Two base classes consume it:

- **`ServiceEntityRepository`** — extends Doctrine bundle's `ServiceEntityRepository`, adds `$cacheable` parameter to `createQueryBuilder()`
- **`EntityRepository`** — extends Doctrine ORM's `EntityRepository`, implements `ServiceEntityRepositoryInterface`

### Custom DBAL type

`DecimalType` overrides Doctrine's `DecimalType` to ensure `convertToPHPValue` always returns `?string`. `ConversionException` provides domain-specific error messages.

### DQL function

`Function\Random` maps `random()` DQL to SQL `random()`.

### Service configuration

Services are autowired and autoconfigured via `src/Resources/config/services.php`. The config loads everything under `src/` and excludes `DependencyInjection`, `Resources`, `ExceptionInterface`, and `Repository` from auto-registration. Bundle wiring: `ChamberOrchestraDoctrineExtensionsBundle` → `ChamberOrchestraDoctrineExtensionsExtension` → `services.php`.

### Namespace → `src/` layout

- `Entity/` — reusable ORM traits (`IdTrait`, `GeneratedIdTrait`, `ToggleTrait`, `SoftDeleteTrait`, `VersionTrait`)
- `Contracts/Entity/` — interfaces for entity traits (`SoftDeleteInterface`, `ToggleInterface`)
- `Filter/` — `SoftDeleteFilter` (Doctrine SQL filter)
- `Repository/` — `EntityRepository`, `ServiceEntityRepository`, `EntityRepositoryTrait`
- `Type/` — `DecimalType` (custom DBAL type)
- `Type/Exception/` — `ConversionException`
- `Function/` — `Random` (DQL function node)
- `Exception/` — `ExceptionInterface` (marker interface)
- `DependencyInjection/` — bundle extension, service loading

## Testing

- Unit tests in `tests/Unit/` mirror the `src/` directory structure
- Integration tests in `tests/Integrational/` use `TestKernel` which boots a minimal Symfony app with FrameworkBundle and ChamberOrchestraDoctrineExtensionsBundle
- PHPUnit config uses `KERNEL_CLASS=Tests\Integrational\TestKernel`
- Test classes use `Test` suffix, placed under matching suite directory

## Code Style

- PHP ^8.5 with `declare(strict_types=1)` in every file
- ChamberOrchestra license header block in every file
- PSR-4 autoloading: `ChamberOrchestra\DoctrineExtensionsBundle\` → `src/`, `Tests\` → `tests/`
- One class/interface/trait per file matching the filename
- CI runs on PHP 8.5 via GitHub Actions (`.github/workflows/php.yml`)

## Dependencies

- Requires PHP ^8.5, Symfony 8.0 components (`framework-bundle`, `clock`, `uid`, `options-resolver`, `runtime`), Doctrine ORM 3, Doctrine Bundle 3.2
- Part of the `chamber-orchestra` bundle ecosystem (sibling: `chamber-orchestra/metadata-bundle`)
