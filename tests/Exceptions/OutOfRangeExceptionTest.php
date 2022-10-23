<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Exceptions\OutOfRangeException;

/**
 * @group exceptions
 * @group out-of-range
 */
class OutOfRangeExceptionTest extends TestCase
{
    public function indexExceptionProvider()
    {
        return [
            '1 of 0 <> 0'     => [1, 0, 0],
            '10 of 0 <> 4'    => [10, 0, 4],
            '-99 of 1 <> 384' => [-99, 1, 384],
        ];
    }

    /**
     * @test
     * @dataProvider indexExceptionProvider
     */
    public function hasTheCorrectMessageForIndexBasedThrows(int $index, int $min, int $max): void
    {
        $exception = OutOfRangeException::index($index, $min, $max);

        self::assertSame(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index, $min, $max
        ), $exception->getMessage());
    }

    public function subsetExceptionProvider()
    {
        return [
            '1:10 of 0 <> 0'    => [1, 10, 0, 0],
            '10:3 of 0 <> 4'    => [10, 3, 0, 4],
            '-99:1 of 1 <> 384' => [-99, 1, 1, 384],
            '1:10 of 0 <> 9'    => [1, 10, 0, 9],
            '10:3 of 0 <> 11'   => [10, 3, 0, 11],
        ];
    }

    /**
     * @test
     * @dataProvider subsetExceptionProvider
     */
    public function hasTheCorrectMessageForSubsetLengthBasedThrows(int $index, int $length, int $min, int $max): void
    {
        $exception = OutOfRangeException::subsetLength($index, $length, $min, $max);

        self::assertSame(sprintf(
            'The subset index %s and length %s, would result in indexes outside the range of %s <> %s',
            $index, $length, $min, $max
        ), $exception->getMessage());
    }
}