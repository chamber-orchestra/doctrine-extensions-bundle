<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integrational;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ContainerSmokeTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testContainerBoots(): void
    {
        self::bootKernel();
        self::assertNotNull(self::getContainer());
    }
}
