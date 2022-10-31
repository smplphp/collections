<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\Contracts\PrioritisedCollection;
use Smpl\Collections\Contracts\PriorityQueue as PriorityDequeContract;
use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\PriorityDeque;
use Smpl\Collections\PriorityQueue;
use Smpl\Utils\Comparators\BaseComparator;
use Smpl\Utils\Comparators\EqualityComparator;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Helpers\ComparisonHelper;
use Smpl\Utils\Predicates\BasePredicate;
use function Smpl\Utils\get_sign;

/**
 * @group mutable
 * @group deque
 * @group priority
 */
class PriorityDequeTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];

    /**
     * @var \Smpl\Collections\PriorityDeque
     */
    private PriorityDeque $deque;

    public function setUp(): void
    {
        $this->deque = new PriorityDeque($this->elements);
    }

    public function queueCreatesFromIterables(): array
    {
        return [
            'From array'         => [[0, 1, 2, 3, 4, 5, 6], 7],
            'From ArrayIterator' => [new ArrayIterator([0, 1, 2, 3, 4, 5, 6]), 7],
            'From Collection'    => [new Collection([0, 1, 2, 3, 4, 5, 6]), 7],
        ];
    }

    /**
     * @test
     * @dataProvider queueCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new PriorityDeque($elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider queueCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $collection = PriorityDeque::of(...$elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     */
    public function usesSimpleIterator(): void
    {
        $iterator = $this->deque->getIterator();

        self::assertInstanceOf(SimpleIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function emptyCollectionsReturnEarly(): void
    {
        $collection = new PriorityDeque();

        self::assertFalse($collection->contains('1'));
        self::assertFalse($collection->containsAll([1]));
        self::assertEquals(0, $collection->countOf(1));
        self::assertNull($collection->peekFirst());
        self::assertNull($collection->pollFirst());
        self::assertNull($collection->peekLast());
        self::assertNull($collection->pollLast());
    }

    public function queueContainsProvider(): array
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
     * @dataProvider queueContainsProvider
     */
    public function knowsWhatItContainsWithoutComparator(int $value, bool $result): void
    {
        self::assertEquals($result, $this->deque->contains($value));
    }

    public function queueContainsComparatorProvider(): array
    {
        $identicalComparator   = new IdenticalityComparator();
        $equalityComparator    = new EqualityComparator();
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
        $creator               = function (Comparator $comparator): PriorityDeque {
            return new PriorityDeque($this->elements, $comparator);
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
            'Equal to 0 as a string'     => ['0', false, $creator($equalityComparator)],
            'Equal to 1 as a string'     => ['1', true, $creator($equalityComparator)],
            'Equal to 2 as a string'     => ['2', true, $creator($equalityComparator)],
            'Equal to 3 as a string'     => ['3', true, $creator($equalityComparator)],
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
     * @dataProvider queueContainsComparatorProvider
     */
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, PriorityDeque $collection): void
    {
        self::assertEquals($result, $collection->contains($value));
    }

    public function queueContainsAllProvider(): array
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
     * @dataProvider queueContainsAllProvider
     */
    public function knowsWhatItContainsAllWithoutComparator(array $value, bool $result): void
    {
        self::assertEquals($result, $this->deque->containsAll($value));
    }

    public function queueContainsAllComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): PriorityDeque {
            return new PriorityDeque($this->elements, $comparator);
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
     * @dataProvider queueContainsAllComparatorProvider
     */
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, PriorityDeque $collection): void
    {
        self::assertEquals($result, $collection->containsAll($value));
    }

    public function queueCountOfProvider(): array
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
     * @dataProvider queueCountOfProvider
     */
    public function countsMatchingElementsWithoutComparator(int $value, int $result): void
    {
        self::assertEquals($result, $this->deque->contains($value));
    }

    public function queueCountOfComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): PriorityDeque {
            return new PriorityDeque($this->elements, $comparator);
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
     * @dataProvider queueCountOfComparatorProvider
     */
    public function countsMatchingElementsWithComparator(int|string $value, int $result, PriorityDeque $collection): void
    {
        self::assertEquals($result, $collection->countOf($value));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->deque->copy([1, 2, 3]);

        self::assertSame($this->deque->toArray(), $this->deque->copy()->toArray());
        self::assertSame([1, 2, 3], $modifiedCopy->toArray());
        self::assertNotSame($this->deque->toArray(), $modifiedCopy->toArray());
    }

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new PriorityDeque())->isEmpty());
        self::assertFalse($this->deque->isEmpty());
    }

    /**
     * @test
     */
    public function canBeConvertedIntoAnArrayList(): void
    {
        $elements = $this->deque->toArray();

        self::assertSame(count($elements), $this->deque->count());
        self::assertTrue(array_is_list($elements));
    }

    public function addElementProvider(): array
    {
        return [
            'Add 11 (new)'       => [11, true, true, 1],
            'Add 12 (new)'       => [12, true, true, 1],
            'Add 13 (new)'       => [13, true, true, 1],
            'Add 10 (duplicate)' => [10, true, true, 2],
        ];
    }

    /**
     * @test
     * @dataProvider addElementProvider
     */
    public function canAddElements(int $value, bool $result, bool $contains, int $count): void
    {
        $collection = $this->deque->copy();

        self::assertEquals($result, $collection->add($value, false));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
    }

    public function addElementFirstProvider(): array
    {
        return [
            'Add 11 (new)'       => [11, true, true, 1],
            'Add 12 (new)'       => [12, true, true, 1],
            'Add 13 (new)'       => [13, true, true, 1],
            'Add 10 (duplicate)' => [10, true, true, 2],
        ];
    }

    /**
     * @test
     * @dataProvider addElementFirstProvider
     */
    public function canAddElementsToTheStart(int $value, bool $result, bool $contains, int $count): void
    {
        $collection = $this->deque->copy();

        self::assertEquals($result, $collection->addFirst($value));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
    }

    public function addElementLastProvider(): array
    {
        return [
            'Add 11 (new)'       => [11, true, true, 1],
            'Add 12 (new)'       => [12, true, true, 1],
            'Add 13 (new)'       => [13, true, true, 1],
            'Add 10 (duplicate)' => [10, true, true, 2],
        ];
    }

    /**
     * @test
     * @dataProvider addElementLastProvider
     */
    public function canAddElementsToTheEnd(int $value, bool $result, bool $contains, int $count): void
    {
        $collection = $this->deque->copy();

        self::assertEquals($result, $collection->addLast($value));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
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
        $collection = $this->deque->copy();

        self::assertEquals($result, $collection->addAll($value, false));

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
        $collection = $this->deque->copy();

        self::assertCount(10, $collection);

        $empty = $collection->clear();

        self::assertCount(0, $empty);
        self::assertTrue($empty->isEmpty());
        self::assertEmpty($empty->toArray());
        self::assertNull($empty->peekFirst());
    }

    public function removeElementProvider(): array
    {
        return [
            'Remove 11 (new)'                                  => [11, false, false, 0, false, 0],
            'Remove 10 (exists)'                               => [10, true, false, 0, true, 1],
            'Remove 10 (exists(3))'                            => [10, true, false, 0, true, 4, [10, 10, 10]],
            'Remove 10 (exists), with equal comparator'        => [10, true, false, 0, true, 1, [], new EqualityComparator()],
            'Remove \'10\' (exists), with equal comparator'    => ['10', true, false, 0, true, 1, [], new EqualityComparator()],
            'Remove \'10\' (exists), without equal comparator' => ['10', false, false, 0, false, 0],
        ];
    }

    /**
     * @test
     * @dataProvider removeElementProvider
     */
    public function canRemoveElements(int|string $value, bool $result, bool $contains, int $count, bool $preContains, int $preCount, array $add = [], ?Comparator $comparator = null): void
    {
        $collection = $this->deque->copy();

        if ($comparator !== null) {
            $collection->setComparator($comparator);
        }

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
        $collection = $this->deque->copy();

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
            'Remove values < 3'                      => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return $value < 3;
                }
            },
                                                         true,
                                                         10,
                                                         8],
            'Remove odd values'                      => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) !== 0;
                }
            },
                                                         true,
                                                         10,
                                                         5],
            'Remove equal values'                    => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) === 0;
                }
            },
                                                         true,
                                                         10,
                                                         5],
            'Remove odd values (adding 1,7,10)'      => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) !== 0;
                }
            },
                                                         true,
                                                         13,
                                                         6,
                                                         [1, 7, 10]],
            'Remove equal values (adding 5,13,7,12)' => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) === 0;
                }
            },
                                                         true,
                                                         14,
                                                         8,
                                                         [5, 13, 7, 12]],
            'Remove values > 10'                     => [new class extends BasePredicate implements Predicate {
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
        $collection = $this->deque->copy();

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
        $collection = $this->deque->copy();

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

    public function addPriorityElementProvider(): array
    {
        return [
            'Add 11 at 0 (new)'                          => [11, 0, 0, true, true, 1],
            'Add 12 at -100 (new)'                       => [12, -100, -100, true, true, 1],
            'Add 13 at 100 (new)'                        => [13, 100, 100, true, true, 1],
            'Add 10 at 3 (duplicate, no priority first)' => [10, 3, null, true, true, 2, PrioritisedCollection::NO_PRIORITY_FIRST],
            'Add 10 at 3 (duplicate, no priority last)'  => [10, 3, 3, true, true, 2, PrioritisedCollection::NO_PRIORITY_LAST],
        ];
    }

    /**
     * @test
     * @dataProvider addPriorityElementProvider
     */
    public function canAddElementsWthPriority(int $value, int $priority, ?int $realPriority, bool $result, bool $contains, int $count, ?int $flags = null): void
    {
        $collection = $this->deque->copy(flags: $flags);
        $total      = $collection->count();

        self::assertEquals($result, $collection->add($value, $priority));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
        self::assertEquals($realPriority, $collection->priority($value));

        if ($result) {
            self::assertCount($total + 1, $collection);
        }
    }

    public function addAllPriorityElementProvider(): array
    {
        return [
            'Add [11, 12, 13] at 0 (new)'                          => [[11, 12, 13], 0, 0, true, true, 1, 13],
            'Add [12, 13, 14] at -100 (new)'                       => [[12, 13, 14], -100, -100, true, true, 1, 13],
            'Add [13, 14, 15] at 100 (new)'                        => [[13, 14, 15], 100, 100, true, true, 1, 13],
            'Add [10, 10, 10] at 3 (duplicate, no priority first)' => [[10, 10, 10], 3, null, true, true, 4, 13, PriorityDeque::NO_PRIORITY_FIRST],
            'Add [10, 10, 10] at 3 (duplicate, no priority last)'  => [[10, 10, 10], 3, 3, true, true, 4, 13, PriorityDeque::NO_PRIORITY_LAST],
        ];
    }

    /**
     * @test
     * @dataProvider addAllPriorityElementProvider
     */
    public function canAddAllElementsWthPriority(array $values, int $priority, ?int $realPriority, bool $result, bool $contains, int $count, int $totalCount, ?int $flags = null): void
    {
        $collection = $this->deque->copy(flags: $flags);

        self::assertEquals($result, $collection->addAll($values, $priority));

        foreach ($values as $value) {
            self::assertEquals($contains, $collection->contains($value));
            self::assertEquals($count, $collection->countOf($value));
            self::assertEquals($realPriority, $collection->priority($value));
        }

        if ($result) {
            self::assertCount($totalCount, $collection);
        }
    }

    public function priorityQueueFlagsProvider(): array
    {
        return [
            'Ascending Order'                     => [PriorityQueue::ASC_ORDER, [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200], [8, 2, 1, 10, null, 9], false],
            'Ascending Order, no null'            => [PriorityQueue::ASC_ORDER | PriorityDeque::NO_NULL, [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200], [8, 2, 1, 10], true],
            'Ascending Order, null first'         => [PriorityQueue::ASC_ORDER | PriorityDeque::NULL_VALUE_FIRST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [null, 8, 2, 1, 10, 9],
                                                      false],
            'Ascending Order, null last'          => [PriorityQueue::ASC_ORDER | PriorityDeque::NULL_VALUE_LAST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [8, 2, 1, 10, 9, null],
                                                      false],
            'Ascending Order, no priority first'  => [PriorityQueue::ASC_ORDER | PriorityDeque::NO_PRIORITY_FIRST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [9, 8, 2, 1, 10, null],
                                                      false],
            'Ascending Order, no priority last'   => [PriorityQueue::ASC_ORDER | PriorityDeque::NO_PRIORITY_LAST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [8, 2, 1, 10, null, 9],
                                                      false],
            'Descending Order'                    => [PriorityQueue::DESC_ORDER, [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200], [null, 10, 1, 2, 8, 9], false],
            'Descending Order, no null'           => [PriorityQueue::DESC_ORDER | PriorityDeque::NO_NULL,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [null, 10, 1, 2, 8, 9],
                                                      true],
            'Descending Order, null first'        => [PriorityQueue::DESC_ORDER | PriorityDeque::NULL_VALUE_FIRST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [null, 10, 1, 2, 8, 9],
                                                      false],
            'Descending Order, null last'         => [PriorityQueue::DESC_ORDER | PriorityDeque::NULL_VALUE_LAST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [10, 1, 2, 8, 9, null],
                                                      false],
            'Descending Order, no priority first' => [PriorityQueue::DESC_ORDER | PriorityDeque::NO_PRIORITY_FIRST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [9, null, 10, 1, 2, 8],
                                                      false],
            'Descending Order, no priority last'  => [PriorityQueue::DESC_ORDER | PriorityDeque::NO_PRIORITY_LAST,
                                                      [1 => 3, 2 => 0, 10 => 100, 8 => -4500, 9 => null, null => 200],
                                                      [null, 10, 1, 2, 8, 9],
                                                      false],
        ];
    }

    /**
     * @test
     * @dataProvider priorityQueueFlagsProvider
     */
    public function handlesFlagsCorrectly(int $flags, array $elements, array $values, bool $throws)
    {
        if ($throws) {
            $this->expectException(InvalidArgumentException::class);
        }

        $deque = new PriorityDeque(flags: $flags);

        foreach ($elements as $element => $priority) {
            $deque->add(empty($element) ? null : $element, $priority);
        }

        self::assertSame($values, $deque->toArray());

        $dequeFlags = $deque->flags();

        self::assertSame(($dequeFlags & $flags), $flags);
    }

    public function priorityQueueInvalidFlagsProvider(): array
    {
        return [
            'Ascending & Descending order'             => [
                PriorityDeque::ASC_ORDER | PriorityDeque::DESC_ORDER,
                'Invalid PrioritisedCollection flags, cannot be ordered both descending as ascending',
            ],
            'No priority first & No priority last'     => [
                PriorityDeque::NO_PRIORITY_FIRST | PriorityDeque::NO_PRIORITY_LAST,
                'Invalid PrioritisedCollection flags, cannot have no priority items at the start and end',
            ],
            'Null elements first & Null elements last' => [
                PriorityDeque::NULL_VALUE_FIRST | PriorityDeque::NULL_VALUE_LAST,
                'Invalid PrioritisedCollection flags, cannot have null items at the start and end',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider priorityQueueInvalidFlagsProvider
     */
    public function errorsWithMutuallyExclusiveFlags(int $flags, string $errorMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessage);

        new PriorityDeque(flags: $flags);
    }

    /**
     * @test
     */
    public function returnsNullForElementPriorityForElementsWithoutPriorities(): void
    {
        self::assertNull($this->deque->priority(1));
        self::assertNull($this->deque->priority(2));
        self::assertNull($this->deque->priority(3));
        self::assertNull($this->deque->priority(4));
    }

    /**
     * @test
     */
    public function returnsFalseForElementPriorityForElementsThatDoNotExist(): void
    {
        self::assertFalse($this->deque->priority(-1000));
        self::assertFalse($this->deque->priority(1000));
        self::assertFalse($this->deque->priority(11));
        self::assertFalse($this->deque->priority(86));
    }

    /**
     * @test
     */
    public function refusesNullForAddWhenSetWithNoNullFlag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Null value passed to a collection that does not accept null values');

        $deque = $this->deque->copy(flags: PrioritisedCollection::NO_NULL);
        $deque->add(null);
    }

    /**
     * @test
     */
    public function refusesNullForAddAllWhenSetWithNoNullFlag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Null value passed to a collection that does not accept null values');

        $deque = $this->deque->copy(flags: PrioritisedCollection::NO_NULL);
        $deque->addAll([1, 3, null, 5, null, 10]);
    }

    /**
     * @test
     */
    public function defaultFlagsAreCorrect(): void
    {
        $deque = new PriorityDeque();

        self::assertSame(($deque->flags() & PrioritisedCollection::ASC_ORDER), PrioritisedCollection::ASC_ORDER);
        self::assertSame(($deque->flags() & PrioritisedCollection::NO_PRIORITY_LAST), PrioritisedCollection::NO_PRIORITY_LAST);
    }

    /**
     * @test
     */
    public function iteratesDestructivelyAscending(): void
    {
        $deque = $this->deque->copy();

        self::assertCount(count($this->elements), $deque);

        foreach ($this->elements as $element) {
            self::assertSame($element, $deque->peekFirst());
            self::assertSame($element, $deque->pollFirst());
        }

        self::assertCount(0, $deque);
    }

    /**
     * @test
     */
    public function iteratesDestructivelyDescending(): void
    {
        $deque    = $this->deque->copy();
        $elements = array_reverse($this->elements);

        self::assertCount(count($elements), $deque);

        foreach ($elements as $element) {
            self::assertSame($element, $deque->peekLast());
            self::assertSame($element, $deque->pollLast());
        }

        self::assertCount(0, $deque);
    }

    /**
     * @test
     */
    public function iteratesDestructivelyAscendingByDefault(): void
    {
        $deque = $this->deque->copy();

        self::assertCount(count($this->elements), $deque);

        foreach ($this->elements as $element) {
            self::assertSame($element, $deque->peek());
            self::assertSame($element, $deque->poll());
        }

        self::assertCount(0, $deque);
    }

    /**
     * @test
     */
    public function returnsAscendingIterator(): void
    {
        $deque    = $this->deque->copy();
        $iterator = $deque->ascendingIterator();
        $elements = $this->elements;

        foreach ($iterator as $i => $element) {
            self::assertSame($elements[$i], $element);
        }
    }

    /**
     * @test
     */
    public function returnsDescendingIterator(): void
    {
        $deque    = $this->deque->copy();
        $iterator = $deque->descendingIterator();
        $elements = array_reverse($this->elements);

        foreach ($iterator as $i => $element) {
            self::assertSame($elements[$i], $element);
        }
    }

    /**
     * @test
     */
    public function convertsToQueue(): void
    {
        $queue = $this->deque->copy()->asQueue();

        self::assertCount(count($this->elements), $queue);

        foreach ($this->elements as $element) {
            self::assertSame($element, $queue->peekFirst());
            self::assertSame($element, $queue->pollFirst());
        }

        self::assertCount(0, $queue);
    }

    /**
     * @test
     */
    public function convertsToStack(): void
    {
        $stack    = $this->deque->copy()->asStack();
        $elements = array_reverse($this->elements);

        self::assertCount(count($elements), $stack);

        foreach ($elements as $element) {
            self::assertSame($element, $stack->peekLast());
            self::assertSame($element, $stack->pollLast());
        }

        self::assertCount(0, $stack);
    }

    /**
     * @test
     */
    public function convertsToPriorityQueue(): void
    {
        $queue = $this->deque->copy()->asPriorityQueue();

        self::assertCount(count($this->elements), $queue);

        foreach ($this->elements as $element) {
            self::assertSame($element, $queue->peekFirst());
            self::assertSame($element, $queue->pollFirst());
        }

        self::assertCount(0, $queue);
    }

    /**
     * @test
     */
    public function convertsToPriorityStack(): void
    {
        $stack    = $this->deque->copy()->asPriorityStack();
        $elements = array_reverse($this->elements);

        self::assertCount(count($elements), $stack);

        foreach ($elements as $element) {
            self::assertSame($element, $stack->peekLast());
            self::assertSame($element, $stack->pollLast());
        }

        self::assertCount(0, $stack);
    }
}