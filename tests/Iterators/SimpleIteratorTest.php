<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Iterators;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Exceptions\OutOfRangeException;
use Smpl\Collections\Iterators\SequenceIterator;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\Sequence;

/**
 * @group iterator
 * @group simple
 */
class SimpleIteratorTest extends TestCase
{
    public function elementIteratorProvider(): array
    {
        $iterator = new SimpleIterator([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);

        return [
            'Index 0'  => [$iterator, 0, 0, true],
            'Index 1'  => [$iterator, 1, 1, true],
            'Index 2'  => [$iterator, 2, 2, true],
            'Index 3'  => [$iterator, 3, 3, true],
            'Index 4'  => [$iterator, 4, 4, true],
            'Index 5'  => [$iterator, 5, 5, true],
            'Index 6'  => [$iterator, 6, 6, true],
            'Index 7'  => [$iterator, 7, 7, true],
            'Index 8'  => [$iterator, 8, 8, true],
            'Index 9'  => [$iterator, 9, 9, true],
            'Index 10' => [$iterator, null, false, false],
            'Index 11' => [$iterator, null, false, false],
        ];
    }

    /**
     * @test
     * @dataProvider elementIteratorProvider
     */
    public function iteratesForAllElements(SimpleIterator $iterator, ?int $key, int|bool $current, bool $valid): void
    {
        self::assertSame($key, $iterator->key());
        self::assertSame($valid, $iterator->valid());
        self::assertSame($current, $iterator->current());

        $iterator->next();
    }

    /**
     * @test
     */
    public function isRewindable(): void
    {
        $iterator = new SimpleIterator([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $data = [
            0  => [0, 0, true],
            1  => [1, 1, true],
            2  => [2, 2, true],
            3  => [3, 3, true],
            4  => [4, 4, true],
            5  => [5, 5, true],
            6  => [6, 6, true],
            7  => [7, 7, true],
            8  => [8, 8, true],
            9  => [9, 9, true],
            10 => [null, false, false],
            11 => [null, false, false],
        ];

        foreach ($data as [$key, $current, $valid]) {
            self::assertSame($key, $iterator->key());
            self::assertSame($current, $iterator->current());
            self::assertSame($valid, $iterator->valid());

            $iterator->next();
        }

        $iterator->rewind();
        self::assertSame(0, $iterator->key());
        self::assertSame(0, $iterator->current());
        self::assertTrue($iterator->valid());
    }
}