<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integrational;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Unit\ClassAvailabilityTest as UnitClassAvailabilityTest;

final class ClassAvailabilityTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    #[DataProviderExternal(UnitClassAvailabilityTest::class, 'provideSymbols')]
    public function testSymbolsAreDefinedInKernel(string $type, string $symbol): void
    {
        self::bootKernel();

        $exists = match ($type) {
            'class' => \class_exists($symbol),
            'interface' => \interface_exists($symbol),
            'trait' => \trait_exists($symbol),
            'enum' => \enum_exists($symbol),
            default => false,
        };

        self::assertTrue($exists, \sprintf('%s %s should exist', $type, $symbol));
    }
}
