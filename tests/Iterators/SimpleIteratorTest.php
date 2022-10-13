<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Iterators;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Iterators\SimpleIterator;

/**
 * @group simple-iterator
 */
class SimpleIteratorTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements;

    protected function setUp(): void
    {
        $this->elements = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    }

    /**
     * @test
     */
    public function iteratesForAllElements(): void
    {
        $iterator = new SimpleIterator($this->elements);
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
    }

    /**
     * @test
     */
    public function isRewindable(): void
    {
        $iterator = new SimpleIterator($this->elements);
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