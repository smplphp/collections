<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Deque;
use Smpl\Collections\Iterators\SimpleIterator;
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
 */
class DequeTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];

    /**
     * @var \Smpl\Collections\Deque
     */
    private Deque $queue;

    public function setUp(): void
    {
        $this->queue = new Deque($this->elements);
    }

    public function queueCreatesFromIterables(): array
    {
        return [
            'From array'         => [[0, 1, 2, 3, 4, 5, 6], 7],
            'From ArrayIterator' => [new ArrayIterator([0, 1, 2, 3, 4, 5, 6]), 7],
            'From Deque'         => [new Deque([0, 1, 2, 3, 4, 5, 6]), 7],
        ];
    }

    /**
     * @test
     * @dataProvider queueCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new Deque($elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider queueCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $collection = Deque::of(...$elements);

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
        $collection = new Deque();

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
        self::assertEquals($result, $this->queue->contains($value));
    }

    public function queueContainsComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): Deque {
            return new Deque($this->elements, $comparator);
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
     * @dataProvider queueContainsComparatorProvider
     */
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, Deque $collection): void
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
        self::assertEquals($result, $this->queue->containsAll($value));
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
        $creator               = function (Comparator $comparator): Deque {
            return new Deque($this->elements, $comparator);
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
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, Deque $collection): void
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
        self::assertEquals($result, $this->queue->contains($value));
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
        $creator               = function (Comparator $comparator): Deque {
            return new Deque($this->elements, $comparator);
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
    public function countsMatchingElementsWithComparator(int|string $value, int $result, Deque $collection): void
    {
        self::assertEquals($result, $collection->countOf($value));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->queue->copy([1, 2, 3]);

        self::assertSame($this->queue->toArray(), $this->queue->copy()->toArray());
        self::assertSame([1, 2, 3], $modifiedCopy->toArray());
        self::assertNotSame($this->queue->toArray(), $modifiedCopy->toArray());
    }

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new Deque())->isEmpty());
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
        $collection = $this->queue->copy();

        self::assertEquals($result, $collection->add($value));
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
        $collection = $this->queue->copy();

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
        $collection = $this->queue->copy();

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
            'Remove \'10\' (exists), without equal comparator' => ['10', false, false, 0, false, 0],
        ];
    }

    /**
     * @test
     * @dataProvider removeElementProvider
     */
    public function canRemoveElements(int|string $value, bool $result, bool $contains, int $count, bool $preContains, int $preCount, array $add = [], ?Comparator $comparator = null): void
    {
        $collection = $this->queue->copy();

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

    /**
     * @test
     */
    public function iteratesDestructivelyAscending(): void
    {
        $queue = $this->queue->copy();

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
    public function iteratesDestructivelyDescending(): void
    {
        $queue    = $this->queue->copy();
        $elements = array_reverse($this->elements);

        self::assertCount(count($elements), $queue);

        foreach ($elements as $element) {
            self::assertSame($element, $queue->peekLast());
            self::assertSame($element, $queue->pollLast());
        }

        self::assertCount(0, $queue);
    }

    /**
     * @test
     */
    public function iteratesDestructivelyAscendingByDefault(): void
    {
        $queue = $this->queue->copy();

        self::assertCount(count($this->elements), $queue);

        foreach ($this->elements as $element) {
            self::assertSame($element, $queue->peek());
            self::assertSame($element, $queue->poll());
        }

        self::assertCount(0, $queue);
    }

    /**
     * @test
     */
    public function returnsAscendingIterator(): void
    {
        $queue    = $this->queue->copy();
        $iterator = $queue->ascendingIterator();
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
        $queue    = $this->queue->copy();
        $iterator = $queue->descendingIterator();
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
        $queue = $this->queue->copy()->asQueue();

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
        $stack    = $this->queue->copy()->asStack();
        $elements = array_reverse($this->elements);

        self::assertCount(count($elements), $stack);

        foreach ($elements as $element) {
            self::assertSame($element, $stack->peekLast());
            self::assertSame($element, $stack->pollLast());
        }

        self::assertCount(0, $stack);
    }
}