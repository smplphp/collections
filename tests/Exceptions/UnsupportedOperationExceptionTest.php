<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Exceptions\OutOfRangeException;
use Smpl\Collections\Exceptions\UnsupportedOperationException;
use Smpl\Collections\ImmutableCollection;

/**
 * @group exceptions
 * @group unsupported-operation
 */
class UnsupportedOperationExceptionTest extends TestCase
{
    public function mutableExceptionProvider()
    {
        return [
            'MyClass::myMethod'                  => ['MyClass', 'myMethod'],
            'MyClass2::myMethod3'                => ['MyClass2', 'myMethod3'],
            ImmutableCollection::class . '::add' => [ImmutableCollection::class, 'add'],
        ];
    }

    /**
     * @test
     * @dataProvider mutableExceptionProvider
     */
    public function hasTheCorrectMessageForMutableBasedThrows(string $class, string $method): void
    {
        $exception = UnsupportedOperationException::mutable($class, $method);

        self::assertSame(sprintf(
            '%s is immutable, and does not support the mutable method %s',
            $class, $method
        ), $exception->getMessage());
    }
}