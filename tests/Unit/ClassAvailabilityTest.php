<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ClassAvailabilityTest extends TestCase
{
    #[DataProvider('provideSymbols')]
    public function testSymbolsAreDefined(string $type, string $symbol): void
    {
        $exists = match ($type) {
            'class' => class_exists($symbol),
            'interface' => interface_exists($symbol),
            'trait' => trait_exists($symbol),
            'enum' => enum_exists($symbol),
            default => false,
        };

        self::assertTrue($exists, sprintf('%s %s should exist', $type, $symbol));
    }

    public static function provideSymbols(): iterable
    {
        yield ['interface', \ChamberOrchestra\DoctrineExtensionsBundle\Exception\ExceptionInterface::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\Type\Exception\ConversionException::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\Type\DecimalType::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\Function\Random::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\Filter\SoftDeleteFilter::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\DependencyInjection\ChamberOrchestraDoctrineExtensionsExtension::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\ChamberOrchestraDoctrineExtensionsBundle::class];

        yield ['interface', \ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\SoftDeleteInterface::class];
        yield ['interface', \ChamberOrchestra\DoctrineExtensionsBundle\Contracts\Entity\ToggleInterface::class];

        yield ['trait', \ChamberOrchestra\DoctrineExtensionsBundle\Entity\ToggleTrait::class];
        yield ['trait', \ChamberOrchestra\DoctrineExtensionsBundle\Entity\IdTrait::class];
        yield ['trait', \ChamberOrchestra\DoctrineExtensionsBundle\Entity\GeneratedIdTrait::class];
        yield ['trait', \ChamberOrchestra\DoctrineExtensionsBundle\Entity\SoftDeleteTrait::class];
        yield ['trait', \ChamberOrchestra\DoctrineExtensionsBundle\Entity\VersionTrait::class];

        yield ['trait', \ChamberOrchestra\DoctrineExtensionsBundle\Repository\EntityRepositoryTrait::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\Repository\ServiceEntityRepository::class];
        yield ['class', \ChamberOrchestra\DoctrineExtensionsBundle\Repository\EntityRepository::class];
    }
}
