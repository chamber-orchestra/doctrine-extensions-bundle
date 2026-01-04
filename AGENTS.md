# Repository Guidelines

## Project Structure & Module Organization
- `src/` contains the bundle source code (PSR-4 namespace `ChamberOrchestra\DoctrineExtensionsBundle`).
- `tests/` is reserved for automated tests; current layout includes `tests/Integrational/` and `tests/Unit/`.
- `bin/` holds helper scripts, if present.
- `vendor/` is Composer-managed dependencies (do not edit).
- Root `composer.json` defines runtime and dev dependencies for the bundle.

## Build, Test, and Development Commands
- `composer install` installs dependencies for local development.
- `composer test` runs the PHPUnit suite via `vendor/bin/phpunit`.
- `vendor/bin/phpunit --filter SomeTest` runs a single test case by name.

## Coding Style & Naming Conventions
- PHP version is `^8.4`; target Symfony 8 and Doctrine ORM 3 APIs.
- Follow PSR-4 autoloading for new classes: `src/Foo/Bar.php` => `ChamberOrchestra\DoctrineExtensionsBundle\Foo\Bar`.
- Use `StudlyCaps` for class names and `camelCase` for methods/properties.
- Keep namespaces aligned with directory names.

## Testing Guidelines
- PHPUnit is configured in `phpunit.xml.dist` with `tests/` as the suite root.
- Prefer separate `tests/Unit/` (fast, isolated) and `tests/Integrational/` (kernel/DB) coverage.
- Name test classes with a `Test` suffix (e.g., `SomethingTest`) and place them under the matching test suite directory.

## Commit & Pull Request Guidelines
- No commit history exists yet; adopt a clear convention such as Conventional Commits (`feat:`, `fix:`).
- Keep commits focused and include context in the body when behavior changes.
- PRs should include a short summary, testing notes (commands run), and linked issues when applicable.

## Configuration Notes
- PHPUnit uses `Tests\Integrational\TestKernel` as `KERNEL_CLASS`; update if the test kernel moves.
