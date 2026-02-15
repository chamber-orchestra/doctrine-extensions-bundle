<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Exception;

use ChamberOrchestra\DoctrineExtensionsBundle\Exception\EntityNotFoundException;
use ChamberOrchestra\DoctrineExtensionsBundle\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

final class EntityNotFoundExceptionTest extends TestCase
{
    public function testImplementsExceptionInterface(): void
    {
        $exception = new EntityNotFoundException();
        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testHasDefaultMessage(): void
    {
        $exception = new EntityNotFoundException();
        self::assertSame('Entity not found.', $exception->getMessage());
    }

    public function testAcceptsCustomMessage(): void
    {
        $exception = new EntityNotFoundException('User not found.');
        self::assertSame('User not found.', $exception->getMessage());
    }
}
