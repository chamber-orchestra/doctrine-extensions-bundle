<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Function;

use ChamberOrchestra\DoctrineExtensionsBundle\Function\Random;
use Doctrine\ORM\Query\SqlWalker;
use PHPUnit\Framework\TestCase;

final class RandomTest extends TestCase
{
    public function testGetSqlReturnsRandomFunction(): void
    {
        $function = new Random('random');
        $walker = $this->createStub(SqlWalker::class);

        self::assertSame('random()', $function->getSql($walker));
    }
}
