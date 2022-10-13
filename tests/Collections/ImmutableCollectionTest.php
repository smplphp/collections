<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Comparators\BaseComparator;
use Smpl\Collections\Comparators\IdenticalComparator;
use Smpl\Collections\Contracts\Comparator;
use Smpl\Collections\Helpers\ComparisonHelper;
use Smpl\Collections\ImmutableCollection;

/**
 * @group immutable
 * @group collection
 */
class ImmutableCollectionTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements;

    /**
     * @var \Smpl\Collections\ImmutableCollection
     */
    private ImmutableCollection $collection;

    public function setUp(): void
    {
        $this->elements   = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];
        $this->collection = new ImmutableCollection($this->elements);
    }

    public function collectionCreatesFromIterables(): array
    {
        return [
            'From array'               => [[0, 1, 2, 3, 4, 5, 6], 7],
            'From ArrayIterator'       => [new ArrayIterator([0, 1, 2, 3, 4, 5, 6]), 7],
            'From ImmutableCollection' => [new ImmutableCollection([0, 1, 2, 3, 4, 5, 6]), 7],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new ImmutableCollection($elements);

        $this->assertCount($count, $collection);
        $this->assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     */
    public function emptyCollectionsReturnEarly(): void
    {
        $collection = new ImmutableCollection();

        $this->assertFalse($collection->contains('1'));
        $this->assertFalse($collection->containsAll([1]));
        $this->assertEquals(0, $collection->countOf(1));
    }

    public function collectionContainsProvider(): array
    {
        return [
            'Contains 0'  => [0, false],
            'Contains 1'  => [1, true],
            'Contains 2'  => [2, true],
            'Contains 3'  => [3, true],
            'Contains 4'  => [4, true],
            'Contains 5'  => [5, true],
            'Contains 6'  => [6, true],
            'Contains 7'  => [7, true],
            'Contains 8'  => [8, true],
            'Contains 9'  => [9, true],
            'Contains 10' => [10, true],
            'Contains 11' => [11, false],
            'Contains 12' => [12, false],
        ];
    }

    /**
     * @test
     * @dataProvider collectionContainsProvider
     */
    public function knowsWhatItContainsWithoutComparator(int $value, bool $result): void
    {
        $this->assertEquals($result, $this->collection->contains($value));
    }

    public function collectionContainsComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalComparator();
        $divisibleByComparator = new class extends BaseComparator {
            public function compare(mixed $a, mixed $b): int
            {
                if ($a < $b) {
                    return ComparisonHelper::LESS_THAN;
                }

                $result = $a % $b;

                return $result === $a
                    ? ComparisonHelper::LESS_THAN
                    : ComparisonHelper::signum($result);
            }
        };

        return [
            'Identical to 0'             => [0, false, $identicalComparator],
            'Identical to 1'             => [1, true, $identicalComparator],
            'Identical to 2'             => [2, true, $identicalComparator],
            'Identical to 3'             => [3, true, $identicalComparator],
            'Identical to 0 as a string' => ['0', false, $identicalComparator],
            'Identical to 1 as a string' => ['1', false, $identicalComparator],
            'Identical to 2 as a string' => ['2', false, $identicalComparator],
            'Identical to 3 as a string' => ['3', false, $identicalComparator],
            'Divisible by 1'             => [1, true, $divisibleByComparator],
            'Divisible by 2'             => [2, true, $divisibleByComparator],
            'Divisible by 3'             => [3, true, $divisibleByComparator],
            'Divisible by 11'            => [11, false, $divisibleByComparator],
            'Divisible by 14'            => [14, false, $divisibleByComparator],
            'Divisible by 15'            => [15, false, $divisibleByComparator],
        ];
    }

    /**
     * @test
     * @dataProvider collectionContainsComparatorProvider
     */
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, Comparator $comparator): void
    {
        $this->assertEquals($result, $this->collection->contains($value, $comparator));
    }

    public function collectionContainsAllProvider(): array
    {
        return [
            'Contains 0, 11, 12' => [[0, 11, 12], false],
            'Contains 1, 2, 3'   => [[1, 2, 3], true],
            'Contains 2, 4, 6'   => [[2, 4, 6], true],
            'Contains 6, 8, 10'  => [[6, 8, 10], true],
            'Contains 0, 1, 2'   => [[0, 1, 2], false],
        ];
    }

    /**
     * @test
     * @dataProvider collectionContainsAllProvider
     */
    public function knowsWhatItContainsAllWithoutComparator(array $value, bool $result): void
    {
        $this->assertEquals($result, $this->collection->containsAll($value));
    }

    public function collectionContainsAllComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalComparator();
        $divisibleByComparator = new class extends BaseComparator {
            public function compare(mixed $a, mixed $b): int
            {
                if ($a < $b) {
                    return ComparisonHelper::LESS_THAN;
                }

                $result = $a % $b;

                return $result === $a
                    ? ComparisonHelper::LESS_THAN
                    : ComparisonHelper::signum($result);
            }
        };

        return [
            'Identical to 0, 11, 12'            => [[0, 11, 12], false, $identicalComparator],
            'Identical to 1, 2, 3'              => [[1, 2, 3], true, $identicalComparator],
            'Identical to 2, 4, 6'              => [[2, 4, 6], true, $identicalComparator],
            'Identical to 6, 8, 10'             => [[6, 8, 10], true, $identicalComparator],
            'Identical to 0, 1, 2'              => [[0, 1, 2], false, $identicalComparator],
            'Identical to 0, 11, 12 as strings' => [['0', '11', '12'], false, $identicalComparator],
            'Identical to 1, 2, 3 as strings'   => [['1', '2', '3'], false, $identicalComparator],
            'Identical to 2, 4, 6 as strings'   => [['2', '4', '6'], false, $identicalComparator],
            'Identical to 6, 8, 10 as strings'  => [['6', '8', '10'], false, $identicalComparator],
            'Identical to 0, 1, 2 as strings'   => [['0', '1', '2'], false, $identicalComparator],
            'Divisible by 1, 2, 3'              => [[1, 2, 3], true, $divisibleByComparator],
            'Divisible by 2, 4'                 => [[2, 4], true, $divisibleByComparator],
            'Divisible by 2, 4, 6'              => [[2, 4, 6], true, $divisibleByComparator],
            'Divisible by 1, 2, 3, 4, 5'        => [[1, 2, 3, 4, 5], true, $divisibleByComparator],
            'Divisible by 6, 7, 8, 9, 10'       => [[6, 7, 8, 9, 10, 11], false, $divisibleByComparator],
        ];
    }

    /**
     * @test
     * @dataProvider collectionContainsAllComparatorProvider
     */
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, Comparator $comparator): void
    {
        $this->assertEquals($result, $this->collection->containsAll($value, $comparator));
    }

    public function collectionCountOfProvider(): array
    {
        return [
            'Contains 0'  => [0, 0],
            'Contains 1'  => [1, 1],
            'Contains 2'  => [2, 1],
            'Contains 3'  => [3, 1],
            'Contains 4'  => [4, 1],
            'Contains 5'  => [5, 1],
            'Contains 6'  => [6, 1],
            'Contains 7'  => [7, 1],
            'Contains 8'  => [8, 1],
            'Contains 9'  => [9, 1],
            'Contains 10' => [10, 1],
            'Contains 11' => [11, 0],
            'Contains 12' => [12, 0],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCountOfProvider
     */
    public function countsMatchingElementsWithoutComparator(int $value, int $result): void
    {
        $this->assertEquals($result, $this->collection->contains($value));
    }

    public function collectionCountOfComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalComparator();
        $divisibleByComparator = new class extends BaseComparator {
            public function compare(mixed $a, mixed $b): int
            {
                if ($a < $b) {
                    return ComparisonHelper::LESS_THAN;
                }

                $result = $a % $b;

                return $result === $a
                    ? ComparisonHelper::LESS_THAN
                    : ComparisonHelper::signum($result);
            }
        };

        return [
            'Identical to 0'             => [0, 0, $identicalComparator],
            'Identical to 1'             => [1, 1, $identicalComparator],
            'Identical to 2'             => [2, 1, $identicalComparator],
            'Identical to 3'             => [3, 1, $identicalComparator],
            'Identical to 0 as a string' => ['0', 0, $identicalComparator],
            'Identical to 1 as a string' => ['1', 0, $identicalComparator],
            'Identical to 2 as a string' => ['2', 0, $identicalComparator],
            'Identical to 3 as a string' => ['3', 0, $identicalComparator],
            'Divisible by 1'             => [1, 10, $divisibleByComparator],
            'Divisible by 2'             => [2, 5, $divisibleByComparator],
            'Divisible by 3'             => [3, 3, $divisibleByComparator],
            'Divisible by 11'            => [11, 0, $divisibleByComparator],
            'Divisible by 14'            => [14, 0, $divisibleByComparator],
            'Divisible by 15'            => [15, 0, $divisibleByComparator],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCountOfComparatorProvider
     */
    public function countsMatchingElementsWithComparator(int|string $value, int $result, Comparator $comparator): void
    {
        $this->assertEquals($result, $this->collection->countOf($value, $comparator));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->collection->copy([1, 2, 3]);

        self::assertSame($this->collection->toArray(), $this->collection->copy()->toArray());
        self::assertSame([1, 2, 3], $modifiedCopy->toArray());
        self::assertNotSame($this->collection->toArray(), $modifiedCopy->toArray());
    }
}