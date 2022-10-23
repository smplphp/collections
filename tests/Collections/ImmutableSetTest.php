<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\Exceptions\UnsupportedOperationException;
use Smpl\Collections\ImmutableSet;
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
 * @group immutable
 * @group set
 */
class ImmutableSetTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];

    /**
     * @var \Smpl\Collections\ImmutableSet
     */
    private ImmutableSet $collection;

    public function setUp(): void
    {
        $this->collection = new ImmutableSet($this->elements);
    }

    public function setCreatesFromIterables(): array
    {
        return [
            'From array'         => [[0, 1, 2, 3, 3, 3, 3], 4],
            'From ArrayIterator' => [new ArrayIterator([0, 1, 2, 3, 4, 5, 6, 0, 1]), 7],
            'From Collection'    => [new Collection([0, 1, 2, 2, 3, 4, 5, 6]), 7],
        ];
    }

    /**
     * @test
     * @dataProvider setCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new ImmutableSet($elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider setCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $collection = ImmutableSet::of(...$elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     */
    public function usesSimpleIterator(): void
    {
        $iterator = $this->collection->getIterator();

        self::assertInstanceOf(SimpleIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function emptyCollectionsReturnEarly(): void
    {
        $collection = new ImmutableSet();

        self::assertFalse($collection->contains('1'));
        self::assertFalse($collection->containsAll([1]));
        self::assertEquals(0, $collection->countOf(1));
    }

    public function setContainsProvider(): array
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
     * @dataProvider setContainsProvider
     */
    public function knowsWhatItContainsWithoutComparator(int $value, bool $result): void
    {
        self::assertEquals($result, $this->collection->contains($value));
    }

    public function setContainsComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): ImmutableSet {
            return new ImmutableSet($this->elements, $comparator);
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
     * @dataProvider setContainsComparatorProvider
     */
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, ImmutableSet $collection): void
    {
        self::assertEquals($result, $collection->contains($value));
    }

    public function setContainsAllProvider(): array
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
     * @dataProvider setContainsAllProvider
     */
    public function knowsWhatItContainsAllWithoutComparator(array $value, bool $result): void
    {
        self::assertEquals($result, $this->collection->containsAll($value));
    }

    public function setContainsAllComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): ImmutableSet {
            return new ImmutableSet($this->elements, $comparator);
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
     * @dataProvider setContainsAllComparatorProvider
     */
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, ImmutableSet $collection): void
    {
        self::assertEquals($result, $collection->containsAll($value));
    }

    public function setCountOfProvider(): array
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
     * @dataProvider setCountOfProvider
     */
    public function countsMatchingElementsWithoutComparator(int $value, int $result): void
    {
        self::assertEquals($result, $this->collection->contains($value));
    }

    public function setCountOfComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): ImmutableSet {
            return new ImmutableSet($this->elements, $comparator);
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
     * @dataProvider setCountOfComparatorProvider
     */
    public function countsMatchingElementsWithComparator(int|string $value, int $result, ImmutableSet $collection): void
    {
        self::assertEquals($result, $collection->countOf($value));
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

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new Collection())->isEmpty());
        self::assertFalse($this->collection->isEmpty());
    }

    /**
     * @test
     */
    public function canBeConvertedIntoAnArrayList(): void
    {
        $elements = $this->collection->toArray();

        self::assertSame(count($elements), $this->collection->count());
        self::assertTrue(array_is_list($elements));
    }

    public function addElementProvider(): array
    {
        return [
            'Add 11 (new)'       => [11],
            'Add 12 (new)'       => [12],
            'Add 13 (new)'       => [13],
            'Add 10 (duplicate)' => [10],
        ];
    }

    /**
     * @test
     * @dataProvider addElementProvider
     */
    public function cannotAddElements(int $value): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->add($value);
    }

    public function addAllElementProvider(): array
    {
        return [
            'Add 11, 12, 13 (new, new, new)'                => [[11, 12, 13]],
            'Add 9, 10, 11 (duplicate, duplicate, new)'     => [[9, 10, 11]],
            'Add 1, 2, 3 (duplicate, duplicate, duplicate)' => [[1, 2, 3]],
        ];
    }

    /**
     * @test
     * @dataProvider addAllElementProvider
     */
    public function cannotAddAllElements(array $value): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->addAll($value);
    }

    /**
     * @test
     */
    public function cannotBeCleared(): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->clear();
    }

    public function removeElementProvider(): array
    {
        return [
            'Remove 11 (new)'                                  => [11],
            'Remove 10 (exists)'                               => [10],
            'Remove 10 (exists(3))'                            => [10],
            'Remove 10 (exists), with equal comparator'        => [10],
            'Remove \'10\' (exists), with equal comparator'    => ['10'],
            'Remove \'10\' (exists), without equal comparator' => ['10'],
        ];
    }

    /**
     * @test
     * @dataProvider removeElementProvider
     */
    public function cannotRemoveElements(int|string $value): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->remove($value);
    }

    public function removeAllElementProvider(): array
    {
        return [
            'Remove 11, 12, 13 (new, new, new)'             => [[11, 12, 13]],
            'Remove 9, 10, 11 (exists, exists, new)'        => [[9, 10, 11]],
            'Remove 1, 2, 3 (exists, exists, exists)'       => [[1, 2, 3]],
            'Remove 1, 2, 3 (exists(3), exists, exists(2))' => [[1, 2, 3]],
            'Remove 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 (exists)' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
        ];
    }

    /**
     * @test
     * @dataProvider removeAllElementProvider
     */
    public function cannotRemoveAllElements(array $value): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->removeAll($value);
    }

    public function removeIfElementProvider(): array
    {
        return [
            'Remove values < 3'                      => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return $value < 3;
                }
            },],
            'Remove odd values'                      => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) !== 0;
                }
            },],
            'Remove equal values'                    => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) === 0;
                }
            },],
            'Remove odd values (adding 1,7,10)'      => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) !== 0;
                }
            },],
            'Remove equal values (adding 5,13,7,12)' => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return ($value % 2) === 0;
                }
            },],
            'Remove values > 10'                     => [new class extends BasePredicate implements Predicate {
                public function test(mixed $value): bool
                {
                    return $value > 10;
                }
            },],
        ];
    }

    /**
     * @test
     * @dataProvider removeIfElementProvider
     */
    public function cannotRemoveElementsByPredicate(Predicate $predicate): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->removeIf($predicate);
    }

    public function retainAllElementProvider(): array
    {
        return [
            'Retain 11, 12, 13 (new, new, new)'             => [[11, 12, 13]],
            'Retain 9, 10, 11 (exists, exists, new)'        => [[9, 10, 11]],
            'Retain 1, 2, 3 (exists, exists, exists)'       => [[1, 2, 3]],
            'Retain 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 (exists)' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
        ];
    }

    /**
     * @test
     * @dataProvider retainAllElementProvider
     */
    public function cannotRetainAllElements(array $value): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->retainAll($value);
    }

    /**
     * @test
     */
    public function cannotHaveItsComparatorSet(): void
    {
        $collection = $this->collection->copy();

        $this->expectException(UnsupportedOperationException::class);

        $collection->setComparator(new EqualityComparator());
    }
}