<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\Exceptions\OutOfRangeException;
use Smpl\Collections\Iterators\SequenceIterator;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\Sequence;
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
 * @group sequence
 */
class SequenceTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];

    /**
     * @var \Smpl\Collections\Sequence
     */
    private Sequence $sequence;

    public function setUp(): void
    {
        $this->sequence = new Sequence($this->elements);
    }

    public function sequenceCreatesFromIterables(): array
    {
        return [
            'From array'         => [[0, 1, 2, 3, 4, 5, 6], 7],
            'From ArrayIterator' => [new ArrayIterator([0, 1, 2, 3, 4, 5, 6]), 7],
            'From Collection'    => [new Collection([0, 1, 2, 3, 4, 5, 6]), 7],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $sequence = new Sequence($elements);

        self::assertCount($count, $sequence);
        self::assertTrue($sequence->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider sequenceCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $sequence = Sequence::of(...$elements);

        self::assertCount($count, $sequence);
        self::assertTrue($sequence->containsAll($elements));
    }

    /**
     * @test
     */
    public function usesSimpleIterator(): void
    {
        $iterator = $this->sequence->getIterator();

        self::assertInstanceOf(SimpleIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function hasSequenceIterator(): void
    {
        $iterator = $this->sequence->getSequenceIterator();

        self::assertInstanceOf(SequenceIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function emptySequencesReturnEarly(): void
    {
        $sequence = new Sequence();

        self::assertFalse($sequence->contains('1'));
        self::assertFalse($sequence->containsAll([1]));
        self::assertEquals(0, $sequence->countOf(1));
        self::assertNull($sequence->find(0, 0));
        self::assertNull($sequence->get(0));
        self::assertNull($sequence->indexOf(0));
        self::assertNull($sequence->lastIndexOf(0));
        self::assertCount(0, $sequence->subset(0));
    }

    public function sequenceContainsProvider(): array
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
     * @dataProvider sequenceContainsProvider
     */
    public function knowsWhatItContainsWithoutComparator(int $value, bool $result): void
    {
        self::assertEquals($result, $this->sequence->contains($value));
    }

    public function sequenceContainsComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): Sequence {
            return new Sequence($this->elements, $comparator);
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
     * @dataProvider sequenceContainsComparatorProvider
     */
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, Sequence $sequence): void
    {
        self::assertEquals($result, $sequence->contains($value));
    }

    public function sequenceContainsAllProvider(): array
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
     * @dataProvider sequenceContainsAllProvider
     */
    public function knowsWhatItContainsAllWithoutComparator(array $value, bool $result): void
    {
        self::assertEquals($result, $this->sequence->containsAll($value));
    }

    public function sequenceContainsAllComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): Sequence {
            return new Sequence($this->elements, $comparator);
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
     * @dataProvider sequenceContainsAllComparatorProvider
     */
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, Sequence $sequence): void
    {
        self::assertEquals($result, $sequence->containsAll($value));
    }

    public function sequenceCountOfProvider(): array
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
     * @dataProvider sequenceCountOfProvider
     */
    public function countsMatchingElementsWithoutComparator(int $value, int $result): void
    {
        self::assertEquals($result, $this->sequence->contains($value));
    }

    public function sequenceCountOfComparatorProvider(): array
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
        $creator               = function (Comparator $comparator): Sequence {
            return new Sequence($this->elements, $comparator);
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
     * @dataProvider sequenceCountOfComparatorProvider
     */
    public function countsMatchingElementsWithComparator(int|string $value, int $result, Sequence $sequence): void
    {
        self::assertEquals($result, $sequence->countOf($value));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->sequence->copy([1, 2, 3]);

        self::assertSame($this->sequence->toArray(), $this->sequence->copy()->toArray());
        self::assertSame([1, 2, 3], $modifiedCopy->toArray());
        self::assertNotSame($this->sequence->toArray(), $modifiedCopy->toArray());
    }

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new Sequence())->isEmpty());
        self::assertFalse($this->sequence->isEmpty());
    }

    /**
     * @test
     */
    public function canBeConvertedIntoAnArrayList(): void
    {
        $elements = $this->sequence->toArray();

        self::assertSame(count($elements), $this->sequence->count());
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
        $sequence = $this->sequence->copy();

        self::assertEquals($result, $sequence->add($value));
        self::assertEquals($contains, $sequence->contains($value));
        self::assertEquals($count, $sequence->countOf($value));
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
        $sequence = $this->sequence->copy();

        self::assertEquals($result, $sequence->addAll($value));

        foreach ($value as $i => $item) {
            self::assertEquals($contains[$i], $sequence->contains($item));
            self::assertEquals($count[$i], $sequence->countOf($item));
        }
    }

    /**
     * @test
     */
    public function canBeCleared(): void
    {
        $sequence = $this->sequence->copy();

        self::assertCount(10, $sequence);

        $empty = $sequence->clear();

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
        $sequence = $this->sequence->copy();

        if ($comparator !== null) {
            $sequence->setComparator($comparator);
        }

        if (! empty($add)) {
            $sequence->addAll($add);
        }

        self::assertEquals($preContains, $sequence->contains($value));
        self::assertEquals($preCount, $sequence->countOf($value));
        self::assertEquals($result, $sequence->remove($value));
        self::assertEquals($contains, $sequence->contains($value));
        self::assertEquals($count, $sequence->countOf($value));
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
        $sequence = $this->sequence->copy();

        if (! empty($add)) {
            $sequence->addAll($add);
        }

        self::assertCount($preCount, $sequence);
        self::assertEquals($result, $sequence->removeAll($value));
        self::assertCount($postCount, $sequence);

        foreach ($value as $item) {
            self::assertFalse($sequence->contains($item));
            self::assertEquals(0, $sequence->countOf($item));
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
        $sequence = $this->sequence->copy();

        if (! empty($add)) {
            $sequence->addAll($add);
        }

        self::assertCount($preCount, $sequence);
        self::assertEquals($result, $sequence->removeIf($predicate));
        self::assertCount($postCount, $sequence);
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
        $sequence = $this->sequence->copy();

        if (! empty($add)) {
            $sequence->addAll($add);
        }

        self::assertCount($preCount, $sequence);
        self::assertEquals($result, $sequence->retainAll($value));
        self::assertCount($postCount, $sequence);

        foreach ($value as $index => $item) {
            self::assertEquals($contains[$index] ?? true, $sequence->contains($item));
            self::assertEquals($count[$index] ?? 1, $sequence->countOf($item));
        }
    }

    public function sequenceFindProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Find 1 starting at 0'  => [$sequence, 1, 0, 0],
            'Find 1 starting at 1'  => [$sequence, 1, 1, 4],
            'Find 1 starting at 5'  => [$sequence, 1, 5, 5],
            'Find 1 starting at 6'  => [$sequence, 1, 6, null],
            'Find 36 starting at 0' => [$sequence, 36, 0, null],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceFindProvider
     */
    public function canFindElementsWithAnIndexOffset(Sequence $sequence, int $element, int $index, ?int $result)
    {
        self::assertSame($result, $sequence->find($element, $index));
    }

    public function sequenceFindWithComparatorProvider(): array
    {
        $sequence   = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);
        $comparator = Comparators::default();

        return [
            'Find 1 starting at 0'  => [$sequence, $comparator, 1, 0, 0],
            'Find 1 starting at 1'  => [$sequence, $comparator, 1, 1, 4],
            'Find 1 starting at 5'  => [$sequence, $comparator, 1, 5, 5],
            'Find 1 starting at 6'  => [$sequence, $comparator, 1, 6, null],
            'Find 36 starting at 0' => [$sequence, $comparator, 36, 0, null],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceFindWithComparatorProvider
     */
    public function canFindElementsWithComparatorAndAnIndexOffset(Sequence $sequence, Comparator $comparator, int $element, int $index, ?int $result)
    {
        $sequence->setComparator($comparator);
        self::assertSame($result, $sequence->find($element, $index));
    }

    public function sequenceFindExceptionProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Find 1 starting at -10' => [$sequence, 1, -10],
            'Find 1 starting at 45'  => [$sequence, 1, 45],
            'Find 1 starting at 13'  => [$sequence, 1, 13],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceFindExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenFindingElementWithIndexOffset(Sequence $sequence, int $element, int $index)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence->find($element, $index);
    }

    public function sequenceFirstProvider(): array
    {
        return [
            'First from [1, 2, 3, 4]'                 => [[1, 2, 3, 4], 1],
            'First from [\'yes\', \'no\', \'maybe\']' => [['yes', 'no', 'maybe'], 'yes'],
            'First from [4, 3, \'eleven\', false]'    => [[4, 3, 'eleven', false], 4],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceFirstProvider
     */
    public function canReturnFirstElement(array $elements, mixed $first)
    {
        self::assertSame($first, Sequence::of(...$elements)->first());
    }

    public function sequenceGetProvider(): array
    {
        return [
            'Get 2 from [1, 2, 3, 4]'                 => [[1, 2, 3, 4], 2, 3],
            'Get 2 from [\'yes\', \'no\', \'maybe\']' => [['yes', 'no', 'maybe'], 2, 'maybe'],
            'Get 3 from [4, 3, \'eleven\', false]'    => [[4, 3, 'eleven', false], 3, false],
            'Get -1 from [1, 2, 3, 4]'                => [[1, 2, 3, 4], -1, 4],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceGetProvider
     */
    public function getsElementByIndex(array $elements, int $index, mixed $result)
    {
        self::assertSame($result, Sequence::of(...$elements)->get($index));
    }

    public function sequenceGetExceptionProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Get -10' => [$sequence, -10],
            'Get 45'  => [$sequence, 45],
            'Get 13'  => [$sequence, 13],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceGetExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenGettingElementByIndex(Sequence $sequence, int $index)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence->get($index);
    }

    public function sequenceHasProvider(): array
    {
        return [
            'Has 2 from [1, 2, 3, 4]'                 => [[1, 2, 3, 4], 2, true],
            'Has 2 from [\'yes\', \'no\', \'maybe\']' => [['yes', 'no', 'maybe'], 2, true],
            'Has 3 from [4, 3, \'eleven\', false]'    => [[4, 3, 'eleven', false], 3, true],
            'Has -1 from [1, 2, 3, 4]'                => [[1, 2, 3, 4], -1, false],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceHasProvider
     */
    public function hasIndex(array $elements, int $index, bool $result)
    {
        self::assertSame($result, Sequence::of(...$elements)->has($index));
    }

    public function sequenceIndexOfProvider(): array
    {
        return [
            'Get for 2 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4, 2, 2], 2, 1],
            'Get for \'not\' from [\'yes\', \'no\', \'maybe\']'             => [['yes', 'no', 'maybe'], 'not', null],
            'Get for \'eleven\' from [4, 3, \'eleven\', \'eleven\', false]' => [[4, 3, 'eleven', 'eleven', false], 'eleven', 2],
            'Get for 4 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4], 4, 3],
            'Get for 1 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4], 1, 0],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceIndexOfProvider
     */
    public function getsFirstIndexForAnElement(array $elements, mixed $element, mixed $result)
    {
        self::assertSame($result, Sequence::of(...$elements)->indexOf($element));
    }

    public function sequenceIndexesOfProvider(): array
    {
        return [
            'Get all for 2 from [1, 2, 3, 4, 2, 2]'                             => [[1, 2, 3, 4, 2, 2], 2, [1, 4, 5]],
            'Get all for \'not\' from [\'yes\', \'no\', \'maybe\']'             => [['yes', 'no', 'maybe'], 'not', []],
            'Get all for \'eleven\' from [4, 3, \'eleven\', \'eleven\', false]' => [[4, 3, 'eleven', 'eleven', false], 'eleven', [2, 3]],
            'Get all for 4 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4], 4, [3]],
            'Get all for 6 from []'                                             => [[], 6, []],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceIndexesOfProvider
     */
    public function getsAllIndexesForAnElement(array $elements, mixed $element, array $result)
    {
        self::assertSame($result, Sequence::of(...$elements)->indexesOf($element)->toArray());
    }

    public function sequenceIndexesOfWithComparatorProvider(): array
    {
        return [
            'Get all for 2 from [1, 2, 3, 4, 2, 2]'                             => [[1, 2, 3, 4, 2, 2], 2, [1, 4, 5]],
            'Get all for \'not\' from [\'yes\', \'no\', \'maybe\']'             => [['yes', 'no', 'maybe'], 'not', []],
            'Get all for \'eleven\' from [4, 3, \'eleven\', \'eleven\', false]' => [[4, 3, 'eleven', 'eleven', false], 'eleven', [2, 3]],
            'Get all for 4 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4], 4, [3]],
            'Get all for 6 from []'                                             => [[], 6, []],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceIndexesOfWithComparatorProvider
     */
    public function getsAllIndexesWithComparatorForAnElement(array $elements, mixed $element, array $result)
    {
        self::assertSame(
            $result,
            Sequence::of(...$elements)
                    ->setComparator(Comparators::default())
                    ->indexesOf($element)
                    ->toArray()
        );
    }

    public function sequenceLastIndexOfProvider(): array
    {
        return [
            'Get for 2 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4, 2, 2], 2, 5],
            'Get for \'not\' from [\'yes\', \'no\', \'maybe\']'             => [['yes', 'no', 'maybe'], 'not', null],
            'Get for \'eleven\' from [4, 3, \'eleven\', \'eleven\', false]' => [[4, 3, 'eleven', 'eleven', false], 'eleven', 3],
            'Get for 4 from [1, 2, 3, 4]'                                   => [[1, 2, 3, 4], 4, 3],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceLastIndexOfProvider
     */
    public function getsLastIndexForAnElement(array $elements, mixed $element, mixed $result)
    {
        self::assertSame($result, Sequence::of(...$elements)->lastIndexOf($element));
    }

    /**
     * @test
     * @dataProvider sequenceLastIndexOfProvider
     */
    public function getsLastIndexWithComparatorForAnElement(array $elements, mixed $element, mixed $result)
    {
        self::assertSame(
            $result,
            Sequence::of(...$elements)
                    ->setComparator(Comparators::default())
                    ->lastIndexOf($element));
    }

    public function sequenceLastProvider(): array
    {
        return [
            'First from [1, 2, 3, 4]'                 => [[1, 2, 3, 4], 4],
            'First from [\'yes\', \'no\', \'maybe\']' => [['yes', 'no', 'maybe'], 'maybe'],
            'First from [4, 3, \'eleven\', false]'    => [[4, 3, 'eleven', false], false],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceLastProvider
     */
    public function canReturnLastElement(array $elements, mixed $first)
    {
        self::assertSame($first, Sequence::of(...$elements)->last());
    }

    public function sequencePutProvider(): array
    {
        return [
            'Put 4 at 3'    => [3, 4],
            'Put 12 at 0'   => [0, 12],
            'Put 100 at 10' => [10, 100],
            'Put -77 at 9'  => [9, -77],
        ];
    }

    /**
     * @test
     * @dataProvider sequencePutProvider
     */
    public function canPutElementsAtProvidedIndex(int $index, int $element): void
    {
        $sequence = $this->sequence->copy();
        $elements = $sequence->toArray();

        $sequence->put($index, $element);

        self::assertSame($element, $sequence->get($index));

        if (isset($elements[$index])) {
            self::assertSame($elements[$index], $sequence->get($index + 1));
        }
    }

    public function sequencePutExceptionProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Put 4 at -10'  => [$sequence, -10, 4],
            'Put 3 at 45'   => [$sequence, 45, 3],
            'Put 100 at 14' => [$sequence, 14, 100],
        ];
    }

    /**
     * @test
     * @dataProvider sequencePutExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenPuttingElementAtIndex(Sequence $sequence, int $index, int $element)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence->put($index, $element);
    }

    public function sequencePutAllProvider(): array
    {
        return [
            'Put [4, 8, 11] at 3'    => [3, [4, 8, 11]],
            'Put [1, 2, 3] at 0'     => [0, [1, 2, 3]],
            'Put [9, 32, 86] at 10'  => [10, [9, 32, 86]],
            'Put [-5, 600, 88] at 9' => [9, [-5, 600, 88]],
        ];
    }

    /**
     * @test
     * @dataProvider sequencePutAllProvider
     */
    public function canPutAllElementsAtProvidedIndex(int $index, array $elements): void
    {
        $sequence         = $this->sequence->copy();
        $existingElements = $sequence->toArray();

        $sequence->putAll($index, $elements);
        $elementIndex = $index;

        foreach ($elements as $element) {
            self::assertSame($element, $sequence->get($elementIndex));
            $elementIndex++;
        }

        if (isset($existingElements[$index])) {
            self::assertSame($existingElements[$index], $sequence->get($index + count($elements)));
        }
    }

    public function sequencePutAllExceptionProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Put [4, 8, 11] at -10' => [$sequence, -10, [4, 8, 11]],
            'Put [1, 2, 3] at 45'   => [$sequence, 45, [1, 2, 3]],
            'Put [9, 32, 86] at 14' => [$sequence, 14, [9, 32, 86]],
        ];
    }

    /**
     * @test
     * @dataProvider sequencePutAllExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenPuttingAllElementAtIndex(Sequence $sequence, int $index, array $elements)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence->putAll($index, $elements);
    }

    public function sequenceSetProvider(): array
    {
        return [
            'Set 4 at 3'   => [3, 4],
            'Set 12 at 0'  => [0, 12],
            'Set 100 at 9' => [9, 100],
            'Set -3 at 10' => [10, -3],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceSetProvider
     */
    public function canSetElementsAtProvidedIndex(int $index, int $element): void
    {
        $sequence     = $this->sequence->copy();
        $elements     = $sequence->toArray();
        $elementCount = $sequence->count();

        $sequence->set($index, $element);

        self::assertSame($element, $sequence->get($index));

        if ($index >= count($elements)) {
            $elementCount += 1;
        }

        self::assertSame($elementCount, $sequence->count());
    }

    public function sequenceSetExceptionProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Put 4 at -10'  => [$sequence, -10, 4],
            'Put 3 at 45'   => [$sequence, 45, 3],
            'Put 100 at 14' => [$sequence, 14, 100],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceSetExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenSettingElementAtIndex(Sequence $sequence, int $index, int $element)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence->set($index, $element);
    }

    public function sequenceSetAllProvider(): array
    {
        return [
            'Set [4, 8, 11] at 3'   => [3, [4, 8, 11], 10],
            'Set [1, 2, 3] at 0'    => [0, [1, 2, 3], 10],
            'Set [9, 32, 86] at 10' => [10, [9, 32, 86], 13],
            'Set [9, 32, 86] at 9'  => [9, [9, 32, 86], 12],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceSetAllProvider
     */
    public function canSetAllElementsAtProvidedIndex(int $index, array $elements, int $count): void
    {
        $sequence = $this->sequence->copy();

        $sequence->setAll($index, $elements);
        $elementIndex = $index;

        foreach ($elements as $element) {
            self::assertSame($element, $sequence->get($elementIndex));
            $elementIndex++;
        }

        self::assertCount($count, $sequence);
    }

    public function sequenceSetAllExceptionProvider(): array
    {
        $sequence = Sequence::of(1, 2, 3, 4, 1, 1, 5, 7, 9, 10, 15, 3, 15);

        return [
            'Set [4, 8, 11] at -10' => [$sequence, -10, [4, 8, 11]],
            'Set [1, 2, 3] at 45'   => [$sequence, 45, [1, 2, 3]],
            'Set [9, 32, 86] at 14' => [$sequence, 14, [9, 32, 86]],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceSetAllExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenSettingAllElementAtIndex(Sequence $sequence, int $index, array $elements)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence->setAll($index, $elements);
    }

    public function sequenceSubsetProvider(): array
    {
        return [
            'Get from 0, no length'   => [0, null, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,]],
            'Get from 1, length of 3' => [1, 3, [2, 3, 4,]],
            'Get from 7, length of 3' => [7, 3, [8, 9, 10,]],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceSubsetProvider
     */
    public function canGetSubsetOfElements(int $index, ?int $length, array $result)
    {
        $subset = $this->sequence->subset($index, $length);

        self::assertCount(count($result), $subset);
        self::assertSame($result, $subset->toArray());
    }

    public function sequenceSubsetExceptionProvider(): array
    {
        return [
            'Get from 10, no length'   => [10, null, true],
            'Get from 11, length of 3' => [11, 3, true],
            'Get from -4, length of 3' => [-4, 3, true],
            'Get from 3, length of 10' => [3, 10, false],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceSubsetExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenGettingSubsetsOfElements(int $index, ?int $length, bool $indexException)
    {
        $this->expectException(OutOfRangeException::class);

        if ($indexException) {
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $this->sequence->count() - 1
            ));
        } else {
            $this->expectExceptionMessage(sprintf(
                'The subset index %s and length %s, would result in indexes outside the range of %s <> %s',
                $index,
                $length,
                0,
                $this->sequence->count() - 1
            ));
        }

        $this->sequence->subset($index, $length);
    }

    /**
     * @test
     */
    public function canGetTheSequenceTail(): void
    {
        $tail     = $this->sequence->tail();
        $elements = $this->sequence->toArray();
        array_shift($elements);

        self::assertCount($this->sequence->count() - 1, $tail);
        self::assertSame($elements, $tail->toArray());
    }

    public function sequenceUnsetProvider(): array
    {
        return [
            'Unset 3' => [3],
            'Unset 0' => [0],
            'Unset 9' => [9],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceUnsetProvider
     */
    public function canUnsetElementsAtProvidedIndex(int $index): void
    {
        $sequence = $this->sequence->copy();
        $elements = $sequence->toArray();

        $sequence->unset($index);

        self::assertCount($this->sequence->count() - 1, $sequence);
        self::assertFalse($sequence->contains($elements[$index]));
    }

    public function sequenceUnsettingExceptionProvider(): array
    {
        return [
            'Unset -10' => [-10],
            'Unset 45'  => [45],
            'Unset 14'  => [14],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceUnsettingExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenUnsettingElementAtIndex(int $index)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $this->sequence->count() - 1
        ));
        $this->sequence->copy()->unset($index);
    }

    public function sequenceArrayIssetProvider(): array
    {
        return [
            '0 in [1, 2, 3, 4]'    => [[1, 2, 3, 4], 0, true],
            '2 in [1, 2, 3, 4]'    => [[1, 2, 3, 4], 2, true],
            '4 in [1, 2, 3, 4]'    => [[1, 2, 3, 4], 4, false],
            '-1 in [1, 2, 3, 4]'   => [[1, 2, 3, 4], -1, false],
            '-100 in [1, 2, 3, 4]' => [[1, 2, 3, 4], -100, false],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceArrayIssetProvider
     */
    public function canBeTreatedAsAnArrayForIsset(array $elements, int $offset, bool $result)
    {
        $sequence = Sequence::of(...$elements);

        self::assertSame($result, isset($sequence[$offset]));
    }

    public function sequenceArrayGetProvider(): array
    {
        return [
            '0 in [1, 2, 3, 4]'    => [[1, 2, 3, 4], 0, 1],
            '2 in [1, 2, 3, 4]'    => [[1, 2, 3, 4], 2, 3],
            '4 in [1, 2, 3, 4]'    => [[1, 2, 3, 4], 4, null, true],
            '-1 in [1, 2, 3, 4]'   => [[1, 2, 3, 4], -1, null, true],
            '-100 in [1, 2, 3, 4]' => [[1, 2, 3, 4], -100, null, true],
        ];
    }

    /**
     * @test
     * @dataProvider sequenceArrayGetProvider
     */
    public function canBeTreatedAsAnArrayForGet(array $elements, int $offset, ?int $result, bool $throwsException = false)
    {
        $sequence = Sequence::of(...$elements);

        if ($throwsException) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $offset,
                0,
                $sequence->count() - 1
            ));
        }

        self::assertSame($result, $sequence[$offset]);
    }

    /**
     * @test
     * @dataProvider sequenceUnsetProvider
     */
    public function canBeTreatedAsAnArrayForUnset(int $index): void
    {
        $sequence = $this->sequence->copy();
        $elements = $sequence->toArray();

        unset($sequence[$index]);

        self::assertCount($this->sequence->count() - 1, $sequence);
        self::assertFalse($sequence->contains($elements[$index]));
    }

    /**
     * @test
     * @dataProvider sequenceUnsettingExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenUnsettingElementAtIndexAsAnArray(int $index)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $this->sequence->count() - 1
        ));
        unset($this->sequence[$index]);
    }

    /**
     * @test
     * @dataProvider sequenceSetProvider
     */
    public function canBeTreatedAsAnArrayForSet(int $index, int $element): void
    {
        $sequence     = $this->sequence->copy();
        $elements     = $sequence->toArray();
        $elementCount = $sequence->count();

        $sequence[$index] = $element;

        self::assertSame($element, $sequence->get($index));

        if ($index >= count($elements)) {
            $elementCount += 1;
        }

        self::assertSame($elementCount, $sequence->count());
    }

    /**
     * @test
     * @dataProvider sequenceSetExceptionProvider
     */
    public function throwsOutOfRangeExceptionWhenSettingElementAtIndexAsAnArray(Sequence $sequence, int $index, int $element)
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index,
            0,
            $sequence->count() - 1
        ));
        $sequence[$index] = $element;
    }
}