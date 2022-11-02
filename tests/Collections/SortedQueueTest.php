<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\SortedQueue;
use Smpl\Utils\Comparators\BaseComparator;
use Smpl\Utils\Comparators\EqualityComparator;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Helpers\ComparisonHelper;
use Smpl\Utils\Predicates\BasePredicate;
use Smpl\Utils\Support\Comparators;
use function Smpl\Utils\get_sign;

/**
 * @group mutable
 * @group queue
 * @group sorted
 */
class SortedQueueTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1,];

    /**
     * @var \Smpl\Collections\SortedQueue
     */
    private SortedQueue $queue;

    public function setUp(): void
    {
        $this->queue = new SortedQueue($this->elements);
    }

    public function collectionCreatesFromIterables(): array
    {
        return [
            'From array'         => [[6, 5, 4, 3, 2, 1, 0], 7],
            'From ArrayIterator' => [new ArrayIterator([6, 5, 4, 3, 2, 1, 0]), 7],
            'From SortedQueue'   => [new Collection([6, 5, 4, 3, 2, 1, 0]), 7],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new SortedQueue($elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider collectionCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $collection = SortedQueue::of(...$elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     */
    public function usesSimpleIterator(): void
    {
        $iterator = $this->queue->getIterator();

        self::assertInstanceOf(SimpleIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function emptyCollectionsReturnEarly(): void
    {
        $collection = new SortedQueue();

        self::assertFalse($collection->contains('1'));
        self::assertFalse($collection->containsAll([1]));
        self::assertEquals(0, $collection->countOf(1));
        self::assertNull($collection->ceiling(0));
        self::assertNull($collection->floor(0));
        self::assertCount(0, $collection->headset(0));
        self::assertNull($collection->higher(0));
        self::assertNull($collection->lower(0));
        self::assertCount(0, $collection->subset(0, 0));
        self::assertCount(0, $collection->tailset(0));
        self::assertNull($collection->pollFirst());
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
        self::assertEquals($result, $this->queue->contains($value));
    }

    public function collectionContainsComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalityComparator();
        $divisibleByComparator = new class extends BaseComparator {
            public function compare(mixed $a, mixed $b): int
            {
                if ($a < $b) {
                    return ComparisonHelper::LESS_THAN;
                }

                $result = $a % $b;

                return $result === $a
                    ? ComparisonHelper::LESS_THAN
                    : get_sign($result);
            }
        };
        $creator               = function (Comparator $comparator): SortedQueue {
            return new SortedQueue($this->elements, $comparator);
        };

        return [
            'Identical to 0'             => [0, false, $creator($identicalComparator)],
            'Identical to 1'             => [1, true, $creator($identicalComparator)],
            'Identical to 2'             => [2, true, $creator($identicalComparator)],
            'Identical to 3'             => [3, true, $creator($identicalComparator)],
            'Identical to 0 as a string' => ['0', false, $creator($identicalComparator)],
            'Identical to 1 as a string' => ['1', false, $creator($identicalComparator)],
            'Identical to 2 as a string' => ['2', false, $creator($identicalComparator)],
            'Identical to 3 as a string' => ['3', false, $creator($identicalComparator)],
            'Divisible by 1'             => [1, true, $creator($divisibleByComparator)],
            'Divisible by 2'             => [2, true, $creator($divisibleByComparator)],
            'Divisible by 3'             => [3, true, $creator($divisibleByComparator)],
            'Divisible by 11'            => [11, false, $creator($divisibleByComparator)],
            'Divisible by 14'            => [14, false, $creator($divisibleByComparator)],
            'Divisible by 15'            => [15, false, $creator($divisibleByComparator)],
        ];
    }

    /**
     * @test
     * @dataProvider collectionContainsComparatorProvider
     */
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, SortedQueue $collection): void
    {
        self::assertEquals($result, $collection->contains($value));
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
        self::assertEquals($result, $this->queue->containsAll($value));
    }

    public function collectionContainsAllComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalityComparator();
        $divisibleByComparator = new class extends BaseComparator {
            public function compare(mixed $a, mixed $b): int
            {
                if ($a < $b) {
                    return ComparisonHelper::LESS_THAN;
                }

                $result = $a % $b;

                return $result === $a
                    ? ComparisonHelper::LESS_THAN
                    : get_sign($result);
            }
        };
        $creator               = function (Comparator $comparator): SortedQueue {
            return new SortedQueue($this->elements, $comparator);
        };

        return [
            'Identical to 0, 11, 12'            => [[0, 11, 12], false, $creator($identicalComparator)],
            'Identical to 1, 2, 3'              => [[1, 2, 3], true, $creator($identicalComparator)],
            'Identical to 2, 4, 6'              => [[2, 4, 6], true, $creator($identicalComparator)],
            'Identical to 6, 8, 10'             => [[6, 8, 10], true, $creator($identicalComparator)],
            'Identical to 0, 1, 2'              => [[0, 1, 2], false, $creator($identicalComparator)],
            'Identical to 0, 11, 12 as strings' => [['0', '11', '12'], false, $creator($identicalComparator)],
            'Identical to 1, 2, 3 as strings'   => [['1', '2', '3'], false, $creator($identicalComparator)],
            'Identical to 2, 4, 6 as strings'   => [['2', '4', '6'], false, $creator($identicalComparator)],
            'Identical to 6, 8, 10 as strings'  => [['6', '8', '10'], false, $creator($identicalComparator)],
            'Identical to 0, 1, 2 as strings'   => [['0', '1', '2'], false, $creator($identicalComparator)],
            'Divisible by 1, 2, 3'              => [[1, 2, 3], true, $creator($divisibleByComparator)],
            'Divisible by 2, 4'                 => [[2, 4], true, $creator($divisibleByComparator)],
            'Divisible by 2, 4, 6'              => [[2, 4, 6], true, $creator($divisibleByComparator)],
            'Divisible by 1, 2, 3, 4, 5'        => [[1, 2, 3, 4, 5], true, $creator($divisibleByComparator)],
            'Divisible by 6, 7, 8, 9, 10'       => [[6, 7, 8, 9, 10, 11], false, $creator($divisibleByComparator)],
        ];
    }

    /**
     * @test
     * @dataProvider collectionContainsAllComparatorProvider
     */
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, SortedQueue $collection): void
    {
        self::assertEquals($result, $collection->containsAll($value));
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
        self::assertEquals($result, $this->queue->contains($value));
    }

    public function collectionCountOfComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalityComparator();
        $divisibleByComparator = new class extends BaseComparator {
            public function compare(mixed $a, mixed $b): int
            {
                if ($a < $b) {
                    return ComparisonHelper::LESS_THAN;
                }

                $result = $a % $b;

                return $result === $a
                    ? ComparisonHelper::LESS_THAN
                    : get_sign($result);
            }
        };
        $creator               = function (Comparator $comparator): SortedQueue {
            return new SortedQueue($this->elements, $comparator);
        };

        return [
            'Identical to 0'             => [0, 0, $creator($identicalComparator)],
            'Identical to 1'             => [1, 1, $creator($identicalComparator)],
            'Identical to 2'             => [2, 1, $creator($identicalComparator)],
            'Identical to 3'             => [3, 1, $creator($identicalComparator)],
            'Identical to 0 as a string' => ['0', 0, $creator($identicalComparator)],
            'Identical to 1 as a string' => ['1', 0, $creator($identicalComparator)],
            'Identical to 2 as a string' => ['2', 0, $creator($identicalComparator)],
            'Identical to 3 as a string' => ['3', 0, $creator($identicalComparator)],
            'Divisible by 1'             => [1, 10, $creator($divisibleByComparator)],
            'Divisible by 2'             => [2, 5, $creator($divisibleByComparator)],
            'Divisible by 3'             => [3, 3, $creator($divisibleByComparator)],
            'Divisible by 11'            => [11, 0, $creator($divisibleByComparator)],
            'Divisible by 14'            => [14, 0, $creator($divisibleByComparator)],
            'Divisible by 15'            => [15, 0, $creator($divisibleByComparator)],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCountOfComparatorProvider
     */
    public function countsMatchingElementsWithComparator(int|string $value, int $result, SortedQueue $collection): void
    {
        self::assertEquals($result, $collection->countOf($value));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->queue->copy([3, 2, 1]);

        self::assertSame($this->queue->toArray(), $this->queue->copy()->toArray());
        self::assertSame([1, 2, 3], $modifiedCopy->toArray());
        self::assertNotSame($this->queue->toArray(), $modifiedCopy->toArray());
    }

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new SortedQueue())->isEmpty());
        self::assertFalse($this->queue->isEmpty());
    }

    /**
     * @test
     */
    public function canBeConvertedIntoAnArrayList(): void
    {
        $elements = $this->queue->toArray();

        self::assertSame(count($elements), $this->queue->count());
        self::assertTrue(array_is_list($elements));
    }

    public function addElementProvider(): array
    {
        return [
            'Add 11 (new)'       => [11, true, true, 1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]],
            'Add 12 (new)'       => [12, true, true, 1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12]],
            'Add 13 (new)'       => [13, true, true, 1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13]],
            'Add 10 (duplicate)' => [10, true, true, 2, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 10]],
            'Add 3 (duplicate)'  => [3, true, true, 2, [1, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10]],
            'Add 0 (new)'        => [0, true, true, 1, [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
        ];
    }

    /**
     * @test
     * @dataProvider addElementProvider
     */
    public function canAddElements(int $value, bool $result, bool $contains, int $count, array $array): void
    {
        $collection = $this->queue->copy();

        self::assertEquals($result, $collection->add($value));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
        self::assertSame($array, $collection->toArray());
    }

    public function addAllElementProvider(): array
    {
        return [
            'Add 11, 12, 13 (new, new, new)'                => [[11, 12, 13], true, [true, true, true], [1, 1, 1]],
            'Add 9, 10, 11 (duplicate, duplicate, new)'     => [[9, 10, 11], true, [true, true, true], [2, 2, 1]],
            'Add 1, 2, 3 (duplicate, duplicate, duplicate)' => [[1, 2, 3], true, [true, true, true], [2, 2, 2]],
        ];
    }

    /**
     * @test
     * @dataProvider addAllElementProvider
     */
    public function canAddAllElements(array $value, bool $result, array $contains, array $count): void
    {
        $collection = $this->queue->copy();

        self::assertEquals($result, $collection->addAll($value));

        foreach ($value as $i => $item) {
            self::assertEquals($contains[$i], $collection->contains($item));
            self::assertEquals($count[$i], $collection->countOf($item));
        }
    }

    /**
     * @test
     */
    public function canBeCleared(): void
    {
        $collection = $this->queue->copy();

        self::assertCount(10, $collection);

        $empty = $collection->clear();

        self::assertCount(0, $empty);
        self::assertTrue($empty->isEmpty());
        self::assertEmpty($empty->toArray());
    }

    public function removeElementProvider(): array
    {
        return [
            'Remove 11 (new)'                                  => [11, false, false, 0, false, 0],
            'Remove 10 (exists)'                               => [10, true, false, 0, true, 1],
            'Remove 10 (exists(3))'                            => [10, true, false, 0, true, 4, [10, 10, 10]],
            'Remove 10 (exists), with equal comparator'        => [10, true, false, 0, true, 1, [], new EqualityComparator()],
            'Remove \'10\' (exists), with equal comparator'    => ['10', true, false, 0, true, 1, [], new EqualityComparator()],
            'Remove \'10\' (exists), without equal comparator' => ['10', true, false, 0, true, 1],
        ];
    }

    /**
     * @test
     * @dataProvider removeElementProvider
     */
    public function canRemoveElements(int|string $value, bool $result, bool $contains, int $count, bool $preContains, int $preCount, array $add = [], ?Comparator $comparator = null): void
    {
        $collection = $this->queue->copy()->setComparator($comparator);

        if (! empty($add)) {
            $collection->addAll($add);
        }

        self::assertEquals($preContains, $collection->contains($value));
        self::assertEquals($preCount, $collection->countOf($value));
        self::assertEquals($result, $collection->remove($value));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
    }

    public function removeAllElementProvider(): array
    {
        return [
            'Remove 11, 12, 13 (new, new, new)'             => [[11, 12, 13], false, 10, 10],
            'Remove 9, 10, 11 (exists, exists, new)'        => [[9, 10, 11], true, 10, 8],
            'Remove 1, 2, 3 (exists, exists, exists)'       => [[1, 2, 3], true, 10, 7],
            'Remove 1, 2, 3 (exists(3), exists, exists(2))' => [[1, 2, 3], true, 13, 7, [1, 1, 3]],
            'Remove 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 (exists)' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10], true, 10, 0],
        ];
    }

    /**
     * @test
     * @dataProvider removeAllElementProvider
     */
    public function canRemoveAllElements(array $value, bool $result, int $preCount, int $postCount, array $add = []): void
    {
        $collection = $this->queue->copy();

        if (! empty($add)) {
            $collection->addAll($add);
        }

        self::assertCount($preCount, $collection);
        self::assertEquals($result, $collection->removeAll($value));
        self::assertCount($postCount, $collection);

        foreach ($value as $item) {
            self::assertFalse($collection->contains($item));
            self::assertEquals(0, $collection->countOf($item));
        }
    }

    public function removeIfElementProvider(): array
    {
        return [
            'Remove values < 3'                      => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return $value < 3;
                }
            },
                                                         true,
                                                         10,
                                                         8],
            'Remove odd values'                      => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) !== 0;
                }
            },
                                                         true,
                                                         10,
                                                         5],
            'Remove equal values'                    => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) === 0;
                }
            },
                                                         true,
                                                         10,
                                                         5],
            'Remove odd values (adding 1,7,10)'      => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) !== 0;
                }
            },
                                                         true,
                                                         13,
                                                         6,
                                                         [1, 7, 10]],
            'Remove equal values (adding 5,13,7,12)' => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) === 0;
                }
            },
                                                         true,
                                                         14,
                                                         8,
                                                         [5, 13, 7, 12]],
            'Remove values > 10'                     => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return $value > 10;
                }
            },
                                                         false,
                                                         10,
                                                         10],
        ];
    }

    /**
     * @test
     * @dataProvider removeIfElementProvider
     */
    public function canRemoveElementsByPredicate(Predicate $predicate, bool $result, int $preCount, int $postCount, array $add = []): void
    {
        $collection = $this->queue->copy();

        if (! empty($add)) {
            $collection->addAll($add);
        }

        self::assertCount($preCount, $collection);
        self::assertEquals($result, $collection->removeIf($predicate));
        self::assertCount($postCount, $collection);
    }

    public function retainAllElementProvider(): array
    {
        return [
            'Retain 11, 12, 13 (new, new, new)'             => [[11, 12, 13], true, 10, 0, [], [false, false, false], [0, 0, 0]],
            'Retain 9, 10, 11 (exists, exists, new)'        => [[9, 10, 11], true, 10, 2, [], [true, true, false], [1, 1, 0]],
            'Retain 1, 2, 3 (exists, exists, exists)'       => [[1, 2, 3], true, 10, 3, [], [true, true, true]],
            'Retain 1, 2, 3 (exists(3), exists, exists(2))' => [[1, 2, 3], true, 13, 6, [1, 1, 3], [true, true, true], [3, 1, 2]],
            'Retain 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 (exists)' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10], false, 10, 10],
        ];
    }

    /**
     * @test
     * @dataProvider retainAllElementProvider
     */
    public function canRetainAllElements(array $value, bool $result, int $preCount, int $postCount, array $add = [], array $contains = [], array $count = []): void
    {
        $collection = $this->queue->copy();

        if (! empty($add)) {
            $collection->addAll($add);
        }

        self::assertCount($preCount, $collection);
        self::assertEquals($result, $collection->retainAll($value));
        self::assertCount($postCount, $collection);

        foreach ($value as $index => $item) {
            self::assertEquals($contains[$index] ?? true, $collection->contains($item));
            self::assertEquals($count[$index] ?? 1, $collection->countOf($item));
        }
    }

    public function collectionCeilingProvider(): array
    {
        return [
            'Least >= 5'    => [5, 5],
            'Least >= 11'   => [11, null],
            'Least >= 8'    => [8, 8],
            'Least >= -400' => [-400, 1],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCeilingProvider
     */
    public function getsTheLeastElementGreaterOrEqualToProvided(int $element, ?int $result): void
    {
        self::assertSame($result, $this->queue->ceiling($element));
    }

    public function collectionFloorProvider(): array
    {
        return [
            'Greatest <= 5'    => [5, 5],
            'Greatest <= 11'   => [11, 10],
            'Greatest <= 8'    => [8, 8],
            'Greatest <= -400' => [-400, null],
        ];
    }

    /**
     * @test
     * @dataProvider collectionFloorProvider
     */
    public function getsTheGreatestElementLessThanOrEqualToProvided(int $element, ?int $result): void
    {
        self::assertSame($result, $this->queue->floor($element));
    }

    public function collectionHeadsetProvider(): array
    {
        return [
            'All < 5 (excl)'    => [5, null, [1, 2, 3, 4]],
            'All < 11 (excl)'   => [11, null, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
            'All < 8 (excl)'    => [8, null, [1, 2, 3, 4, 5, 6, 7]],
            'All < -400 (excl)' => [-400, null, []],
            'All < 5 (incl)'    => [5, true, [1, 2, 3, 4, 5]],
            'All < 11 (incl)'   => [11, true, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
            'All < 8 (incl)'    => [8, true, [1, 2, 3, 4, 5, 6, 7, 8]],
            'All < -400 (incl)' => [-400, true, []],
        ];
    }

    /**
     * @test
     * @dataProvider collectionHeadsetProvider
     */
    public function getAllElementsThatAreLessThanTheProvided(int $element, ?bool $inclusive, array $result): void
    {
        if ($inclusive !== null) {
            $headset = $this->queue->headset($element, $inclusive);
        } else {
            $headset = $this->queue->headset($element);
        }

        if (empty($result)) {
            self::assertTrue($headset->isEmpty());
        } else {
            self::assertTrue($headset->containsAll($result));
        }

        self::assertSame($result, $headset->toArray());
    }

    public function collectionHigherProvider(): array
    {
        return [
            'Least > 5'    => [5, 6],
            'Least > 11'   => [11, null],
            'Least > 8'    => [8, 9],
            'Least > -400' => [-400, 1],
        ];
    }

    /**
     * @test
     * @dataProvider collectionHigherProvider
     */
    public function getsTheLeastElementGreaterThanTheProvided(int $element, ?int $result): void
    {
        self::assertSame($result, $this->queue->higher($element));
    }

    public function collectionLowerProvider(): array
    {
        return [
            'Greatest < 5'    => [5, 4],
            'Greatest < 11'   => [11, 10],
            'Greatest < 8'    => [8, 7],
            'Greatest < -400' => [-400, null],
        ];
    }

    /**
     * @test
     * @dataProvider collectionLowerProvider
     */
    public function getsTheGreatestElementLessThanTheProvided(int $element, ?int $result): void
    {
        self::assertSame($result, $this->queue->lower($element));
    }

    public function collectionSubsetProvider(): array
    {
        return [
            'Subset 5 (incl) to 9 (excl)' => [5, 9, true, null, [5, 6, 7, 8]],
            'Subset 5 (excl) to 9 (incl)' => [5, 9, null, true, [6, 7, 8, 9]],
            'Subset 5 (excl) to 9 (excl)' => [5, 9, null, null, [6, 7, 8,]],
            'Subset 5 (incl) to 9 (incl)' => [5, 9, true, true, [5, 6, 7, 8, 9]],
        ];
    }

    /**
     * @test
     * @dataProvider collectionSubsetProvider
     */
    public function getAllElementsBetweenTheProvidedProvided(int $fromElement, int $toElement, ?bool $fromInclusive, ?bool $toInclusive, array $result): void
    {
        if ($fromInclusive !== null) {
            if ($toInclusive !== null) {
                $subset = $this->queue->subset($fromElement, $toElement, $fromInclusive, $toInclusive);
            } else {
                $subset = $this->queue->subset($fromElement, $toElement, $fromInclusive);
            }
        } else if ($toInclusive !== null) {
            $subset = $this->queue->subset($fromElement, $toElement, toInclusive: $toInclusive);
        } else {
            $subset = $this->queue->subset($fromElement, $toElement);
        }

        self::assertSame($result, $subset->toArray());
    }

    public function collectionTailsetProvider(): array
    {
        return [
            'All > 5 (excl)'    => [5, null, [6, 7, 8, 9, 10]],
            'All > 11 (excl)'   => [11, null, []],
            'All > 8 (excl)'    => [8, null, [9, 10]],
            'All > -400 (excl)' => [-400, null, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
            'All > 5 (incl)'    => [5, true, [5, 6, 7, 8, 9, 10]],
            'All > 11 (incl)'   => [11, true, []],
            'All > 8 (incl)'    => [8, true, [8, 9, 10]],
            'All > -400 (incl)' => [-400, true, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
        ];
    }

    /**
     * @test
     * @dataProvider collectionTailsetProvider
     */
    public function getAllElementsThatAreGreaterThanTheProvided(int $element, ?bool $inclusive, array $result): void
    {
        if ($inclusive !== null) {
            $tailset = $this->queue->tailset($element, $inclusive);
        } else {
            $tailset = $this->queue->tailset($element);
        }

        if (empty($result)) {
            self::assertTrue($tailset->isEmpty());
        } else {
            self::assertTrue($tailset->containsAll($result));
        }

        self::assertSame($result, $tailset->toArray());
    }

    /**
     * @test
     */
    public function sortsElementsAfterSettingNewComparator(): void
    {
        $collection = $this->queue->copy();
        $collection->setComparator(new class extends BaseComparator {

            public function compare(mixed $a, mixed $b): int
            {
                return ComparisonHelper::flip(Comparators::default()->compare($a, $b));
            }
        });

        self::assertSame([10, 9, 8, 7, 6, 5, 4, 3, 2, 1,], $collection->toArray());
    }

    /**
     * @test
     */
    public function iteratesDestructively(): void
    {
        $queue    = $this->queue->copy();
        $elements = $queue->toArray();

        self::assertCount(count($elements), $queue);

        foreach ($elements as $element) {
            self::assertSame($element, $queue->peekFirst());
            self::assertSame($element, $queue->pollFirst());
        }

        self::assertCount(0, $queue);
    }
}