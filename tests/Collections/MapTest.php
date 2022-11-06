<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Contracts\Hashable;
use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\Map;
use Smpl\Utils\Comparators\BaseComparator;
use Smpl\Utils\Comparators\EqualityComparator;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\BiFunc;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Func;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Contracts\ReturnsValue;
use Smpl\Utils\Contracts\Supplier;
use Smpl\Utils\Functional\BaseBiFunc;
use Smpl\Utils\Functional\BaseFunc;
use Smpl\Utils\Functional\BaseSupplier;
use Smpl\Utils\Helpers\ComparisonHelper;
use Smpl\Utils\Predicates\BasePredicate;
use function Smpl\Utils\get_sign;

/**
 * @group mutable
 * @group map
 */
class MapTest extends TestCase
{
    /**
     * @var array<string, int>
     */
    private array $elements = [
        'one'   => 1,
        'two'   => 2,
        'three' => 3,
        'four'  => 4,
        'five'  => 5,
        'six'   => 6,
        'seven' => 7,
        'eight' => 8,
        'nine'  => 9,
        'ten'   => 10,
    ];

    /**
     * @var \Smpl\Collections\Map
     */
    private Map $map;

    public function setUp(): void
    {
        $this->map = new Map($this->elements);
    }

    public function collectionCreatesFromIterables(): array
    {
        $elements = [
            'one'   => 1,
            'two'   => 2,
            'three' => 3,
            'four'  => 4,
            'five'  => 5,
            'six'   => 6,
            'seven' => 7,
        ];
        return [
            'From array'         => [$elements, 7],
            'From ArrayIterator' => [new ArrayIterator($elements), 7],
            'From Map'           => [new Map($elements), 7],
        ];
    }

    /**
     * @test
     * @dataProvider collectionCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new Map($elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider collectionCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $collection = Map::of(...$elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     */
    public function usesSimpleIterator(): void
    {
        $iterator = $this->map->getIterator();

        self::assertInstanceOf(SimpleIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function emptyCollectionsReturnEarly(): void
    {
        $collection = new Map();

        self::assertFalse($collection->contains('1'));
        self::assertFalse($collection->containsAll([1]));
        self::assertEquals(0, $collection->countOf(1));
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
        self::assertEquals($result, $this->map->contains($value));
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
        $creator               = function (Comparator $comparator): Map {
            return new Map($this->elements, $comparator);
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
    public function knowsWhatItContainsWithComparator(int|string $value, bool $result, Map $collection): void
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
        self::assertEquals($result, $this->map->containsAll($value));
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
        $creator               = function (Comparator $comparator): Map {
            return new Map($this->elements, $comparator);
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
    public function knowsWhatItContainsAllWithComparator(array $value, bool $result, Map $collection): void
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
        self::assertEquals($result, $this->map->contains($value));
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
        $creator               = function (Comparator $comparator): Map {
            return new Map($this->elements, $comparator);
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
    public function countsMatchingElementsWithComparator(int|string $value, int $result, Map $collection): void
    {
        self::assertEquals($result, $collection->countOf($value));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->map->copy([1, 2, 3]);

        self::assertSame($this->map->toArray(), $this->map->copy()->toArray());
        self::assertSame([1, 2, 3], $modifiedCopy->toArray());
        self::assertNotSame($this->map->toArray(), $modifiedCopy->toArray());
    }

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new Map())->isEmpty());
        self::assertFalse($this->map->isEmpty());
    }

    /**
     * @test
     */
    public function canBeConvertedIntoAnArray(): void
    {
        $elements = $this->map->toArray();

        self::assertSame(count($elements), $this->map->count());
        self::assertFalse(array_is_list($elements));
    }

    public function addElementProvider(): array
    {
        return [
            'Add 11 (new)'                  => ['eleven', 11, true, true, 1],
            'Add 12 (new)'                  => ['twelve', 12, true, true, 1],
            'Add 13 (new)'                  => ['thirteen', 13, true, true, 1],
            'Add 10 (duplicate)'            => ['ten', 10, true, true, 1],
            'Add Hashable object (new)'     => [
                null,
                new class implements Hashable {
                    public function getHashCode(): string
                    {
                        return 'fourteen';
                    }
                },
                true,
                true,
                1,
            ]
        ];
    }

    /**
     * @test
     * @dataProvider addElementProvider
     */
    public function canAddElements(?string $key, int|object $value, bool $result, bool $contains, int $count): void
    {
        $collection = $this->map->copy();

        self::assertEquals($result, $collection->add($value, $key));
        self::assertEquals($contains, $collection->contains($value));

        if ($key !== null) {
            self::assertEquals($contains, $collection->has($key));
        }

        self::assertEquals($count, $collection->countOf($value));
    }

    /**
     * @test
     */
    public function throwsExceptionWhenAddingElementWithoutKeyOrWayToRetrieveOne(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Map values must be added with a key, or be an object implementing %s',
            Hashable::class
        ));

        $this->map->add(56);
    }

    public function addAllElementProvider(): array
    {
        return [
            'Add 11, 12, 13 (new, new, new)'                => [['eleven' => 11, 'twelve' => 12, 'thirteen' => 13], true, [true, true, true], [1, 1, 1]],
            'Add 9, 10, 11 (duplicate, duplicate, new)'     => [['nine' => 9, 'ten' => 10, 'eleven' => 11], true, [true, true, true], [1, 1, 1]],
            'Add 1, 2, 3 (duplicate, duplicate, duplicate)' => [['one' => 1, 'two' => 2, 'three' => 3], true, [true, true, true], [1, 1, 1]],
        ];
    }

    /**
     * @test
     * @dataProvider addAllElementProvider
     */
    public function canAddAllElements(array $value, bool $result, array $contains, array $count): void
    {
        $collection = $this->map->copy();

        self::assertEquals($result, $collection->addAll($value));

        $values = array_values($value);

        foreach ($values as $i => $item) {
            self::assertEquals($contains[$i], $collection->contains($item));
            self::assertEquals($count[$i], $collection->countOf($item));
        }
    }

    /**
     * @test
     */
    public function canBeCleared(): void
    {
        $collection = $this->map->copy();

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
        $collection = $this->map->copy();

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
        $collection = $this->map->copy();

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
        $collection = $this->map->copy();

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
        $collection = $this->map->copy();

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

    public function putElementProvider(): array
    {
        return [
            'Put 11 (new)'       => ['eleven', 11, true, 1],
            'Put 12 (new)'       => ['twelve', 12, true, 1],
            'Put 13 (new)'       => ['thirteen', 13, true, 1],
            'Put 10 (duplicate)' => ['ten', 10, true, 1],
        ];
    }

    /**
     * @test
     * @dataProvider putElementProvider
     */
    public function canPutElements(string $key, int $value, bool $contains, int $count): void
    {
        $collection = $this->map->copy();

        $collection->put($key, $value);

        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($contains, $collection->has($key));
        self::assertEquals($count, $collection->countOf($value));
    }

    public function putAllElementProvider(): array
    {
        return [
            'Put 11, 12, 13 (new, new, new)'                => [['eleven' => 11, 'twelve' => 12, 'thirteen' => 13], [true, true, true], [1, 1, 1]],
            'Put 9, 10, 11 (duplicate, duplicate, new)'     => [['nine' => 9, 'ten' => 10, 'eleven' => 11], [true, true, true], [1, 1, 1]],
            'Put 1, 2, 3 (duplicate, duplicate, duplicate)' => [['one' => 1, 'two' => 2, 'three' => 3], [true, true, true], [1, 1, 1]],
        ];
    }

    /**
     * @test
     * @dataProvider putAllElementProvider
     */
    public function canPutAllElements(array $value, array $contains, array $count): void
    {
        $collection = $this->map->copy();

        $collection->putAll($value);
        $keys   = array_keys($value);
        $values = array_values($value);

        self::assertTrue($collection->hasAll($keys));
        self::assertTrue($collection->containsAll($values));

        foreach ($values as $i => $item) {
            self::assertEquals($count[$i], $collection->countOf($item));
        }
    }

    public function collectionHasAllProvider(): array
    {
        return [
            'Contains [\'one\', \'eleven\', \'twelve\']' => [['one', 'eleven', 'twelve'], false],
            'Contains [\'one\', \'two\', \'three\']'     => [['one', 'two', 'three'], true],
            'Contains [\'two\', \'four\', \'six\']'      => [['two', 'four', 'six'], true],
            'Contains [\'six\', \'eight\', \'ten\']'     => [['six', 'eight', 'ten'], true],
            'Contains [\'zero\', \'one\', \'two\']'      => [['zero', 'one', 'two'], false],
        ];
    }

    /**
     * @test
     * @dataProvider collectionHasAllProvider
     */
    public function knowsWhatItHasAllWithoutComparator(array $keys, bool $result): void
    {
        self::assertEquals($result, $this->map->hasAll($keys));
    }

    public function forgetElementProvider(): array
    {
        return [
            'Forget \'eleven\' (new)'                        => ['eleven', false, false],
            'Forget \'ten\' (exists)'                        => ['ten', false, true],
            'Forget \'ten\' (exists), with equal comparator' => ['ten', false, true, new EqualityComparator()],
            'Forget 10 (new), with equal comparator'         => [10, false, false, new EqualityComparator()],
            'Forget 10 (new), without equal comparator'      => [10, false, false],
        ];
    }

    /**
     * @test
     * @dataProvider forgetElementProvider
     */
    public function canForgetElements(int|string $key, bool $has, bool $preHas, ?Comparator $comparator = null): void
    {
        $collection = $this->map->copy();

        if ($comparator !== null) {
            $collection->setComparator($comparator);
        }

        self::assertEquals($preHas, $collection->has($key));
        $collection->forget($key);

        self::assertEquals($has, $collection->has($key));
    }

    public function forgetAllElementProvider(): array
    {
        return [
            'Forget [\'eleven\', \'twelve\', \'thirteen\'] (new, new, new)'                                                       => [['eleven', 'twelve', 'thirteen'], 10, 10],
            'Forget [\'nine\', \'ten\', \'eleven\'] (exists, exists, new)'                                                        => [['nine', 'ten', 'eleven'], 10, 8],
            'Forget [\'one\', \'two\', \'three\'] (exists, exists, exists)'                                                       => [['one', 'two', 'three'], 10, 7],
            'Forget [\'one\', \'two\', \'three\'] (exists(3), exists, exists(2))'                                                 => [['one', 'two', 'three'], 10, 7],
            'Forget [\'one\', \'two\', \'three\', \'four\', \'five\', \'six\', \'seven\', \'eight\', \'nine\', \'ten\'] (exists)' => [['one',
                                                                                                                                       'two',
                                                                                                                                       'three',
                                                                                                                                       'four',
                                                                                                                                       'five',
                                                                                                                                       'six',
                                                                                                                                       'seven',
                                                                                                                                       'eight',
                                                                                                                                       'nine',
                                                                                                                                       'ten'],
                                                                                                                                      10,
                                                                                                                                      0],
        ];
    }

    /**
     * @test
     * @dataProvider forgetAllElementProvider
     */
    public function canForgetAllElements(array $value, int $preCount, int $postCount): void
    {
        $collection = $this->map->copy();

        self::assertCount($preCount, $collection);
        $collection->forgetAll($value);
        self::assertCount($postCount, $collection);

        foreach ($value as $item) {
            self::assertFalse($collection->has($item));
        }
    }

    public function forgetIfElementProvider(): array
    {
        return [
            'Forget values < 3'                      => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return $value->getValue() < 3;
                }
            },
                                                         10,
                                                         8],
            'Forget odd values'                      => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value->getValue() % 2) !== 0;
                }
            },
                                                         10,
                                                         5],
            'Forget equal values'                    => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value->getValue() % 2) === 0;
                }
            },
                                                         10,
                                                         5],
            'Forget odd values (adding 1,7,10)'      => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value->getValue() % 2) !== 0;
                }
            },
                                                         10,
                                                         5,
                                                         ['one', 'seven', 'ten']],
            'Forget equal values (adding 5,13,7,12)' => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return ($value->getValue() % 2) === 0;
                }
            },
                                                         10,
                                                         5,
                                                         ['five', 'thirteen', 'seven', 'twelve']],
            'Forget values > 10'                     => [new class extends BasePredicate {
                public function test(mixed $value): bool
                {
                    return $value->getValue() > 10;
                }
            },
                                                         10,
                                                         10],
        ];
    }

    /**
     * @test
     * @dataProvider forgetIfElementProvider
     */
    public function canForgetElementsByPredicate(Predicate $predicate, int $preCount, int $postCount): void
    {
        $collection = $this->map->copy();

        self::assertCount($preCount, $collection);
        $collection->forgetIf($predicate);
        self::assertCount($postCount, $collection);
    }

    public function keepAllElementProvider(): array
    {
        return [
            'Keep 11, 12, 13 (new, new, new)'             => [['eleven', 'twelve', 'thirteen'], 10, 0, [], [false, false, false], [0, 0, 0]],
            'Keep 9, 10, 11 (exists, exists, new)'        => [['nine', 'ten', 'eleven'], 10, 2, [], [true, true, false], [1, 1, 0]],
            'Keep 1, 2, 3 (exists, exists, exists)'       => [['one', 'two', 'three'], 10, 3, [], [true, true, true]],
            'Keep 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 (exists)' => [['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'], 10, 10],
        ];
    }

    /**
     * @test
     * @dataProvider keepAllElementProvider
     */
    public function canKeepAllElements(array $value, int $preCount, int $postCount, array $add = [], array $contains = [], array $count = []): void
    {
        $collection = $this->map->copy();

        if (! empty($add)) {
            $collection->addAll($add);
        }

        self::assertCount($preCount, $collection);
        $collection->keepAll($value);
        self::assertCount($postCount, $collection);

        foreach ($value as $index => $item) {
            self::assertEquals($contains[$index] ?? true, $collection->has($item));
        }
    }

    public function getElementProvider(): array
    {
        return [
            'one'          => ['one', 1],
            'two'          => ['two', 2],
            'eleven'       => ['eleven', null],
            'four-hundred' => ['four-hundred', null],
            '6'            => [6, null],
            '8'            => [8, null],
            'false'        => [false, null],
        ];
    }

    /**
     * @test
     * @dataProvider getElementProvider
     */
    public function canGetElements(mixed $key, mixed $result): void
    {
        self::assertSame($result, $this->map->get($key));
    }

    /**
     * @test
     */
    public function canGetKeys(): void
    {
        self::assertSame(array_keys($this->elements), $this->map->keys()->toArray());
    }

    /**
     * @test
     */
    public function canGetValues(): void
    {
        self::assertSame(array_values($this->elements), $this->map->values()->toArray());
    }

    public function replaceElementProvider(): array
    {
        return [
            'Replace \'eleven\' with 11 (new)'         => ['eleven', 11, false],
            'Replace \'nine\' with 99'                 => ['nine', 99, true],
            'Replace \'thirteen\' with 12 (new)'       => ['thirteen', 12, false],
            'Replace \'ten\' with \'ten\' (duplicate)' => ['ten', 'ten', true],
        ];
    }

    /**
     * @test
     * @dataProvider replaceElementProvider
     */
    public function canReplaceElements(string $key, int|string $value, bool $result): void
    {
        $collection = $this->map->copy();

        self::assertEquals($result, $collection->replace($key, $value));
        self::assertEquals($result, $collection->has($key));

        if ($result) {
            self::assertSame($value, $collection->get($key));
        } else {
            self::assertNull($collection->get($key));
        }
    }

    public function replaceWithElementProvider(): array
    {
        return [
            'Replace \'eleven\' with 11 (new)'       => [
                'eleven',
                new class extends BaseSupplier {
                    public function get(): int
                    {
                        return 11;
                    }
                },
                false,
            ],
            'Replace \'nine\' with 99 (exists)'      => [
                'nine',
                new class extends BaseSupplier {
                    public function get(): int
                    {
                        return 99;
                    }
                },
                true,
            ],
            'Replace \'thirteen\' with 12 (new)'     => [
                'thirteen',
                new class extends BaseFunc {
                    public function apply(mixed $value): int
                    {
                        return 12;
                    }
                },
                false,
            ],
            'Replace \'three\' with 999977 (exists)' => [
                'three',
                new class extends BaseFunc {
                    public function apply(mixed $value): int
                    {
                        return 999977;
                    }
                },
                true,
            ],
            'Replace \'ten\' with \'ten\' (exists)'  => [
                'ten',
                new class extends BaseBiFunc {
                    public function apply(mixed $arg1, mixed $arg2): string
                    {
                        return 'ten';
                    }
                },
                true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider replaceWithElementProvider
     */
    public function canReplaceWithElements(string $key, ReturnsValue $valueRetriever, bool $result): void
    {
        $collection = $this->map->copy();

        if ($valueRetriever instanceof Supplier) {
            $value = $valueRetriever->get();
        } else if ($valueRetriever instanceof Func) {
            $value = $valueRetriever->apply($collection->get($key));
        } else if ($valueRetriever instanceof BiFunc) {
            $value = $valueRetriever->apply($key, $collection->get($key));
        }

        self::assertEquals($result, $collection->replaceWith($key, $valueRetriever));
        self::assertEquals($result, $collection->has($key));

        if ($result) {
            self::assertSame($value, $collection->get($key));
        } else {
            self::assertNull($collection->get($key));
        }
    }

    /**
     * @test
     */
    public function throwsExceptionWhenUnableToRetrieveValueToReplaceCurrent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Value retriever did not yield a value to replace key \'%s\'',
            'one'
        ));

        $this->map->replaceWith('one', new class implements ReturnsValue {
        });
    }

    public function replaceIfElementProvider(): array
    {
        return [
            'Replace \'eleven\' with 11 if even (new)'             => [
                'eleven',
                11,
                new class extends BasePredicate {
                    public function test(mixed $value): bool
                    {
                        return ($value % 2) === 0;
                    }
                },
                false,
            ],
            'Replace \'nine\' with 99 if odd (exists)'             => [
                'nine',
                99,
                new class extends BasePredicate {
                    public function test(mixed $value): bool
                    {
                        return ($value % 2) !== 0;
                    }
                },
                true,
            ],
            'Replace \'thirteen\' with 12 if divisible by 3 (new)' => [
                'thirteen',
                12,
                new class extends BasePredicate {
                    public function test(mixed $value): bool
                    {
                        return ($value % 3) === 0;
                    }
                },
                false,
            ],
            'Replace \'three\' with 999977 if true (exists)'       => [
                'three',
                999977,
                new class extends BasePredicate {
                    public function test(mixed $value): bool
                    {
                        return true;
                    }
                },
                true,
            ],
            'Replace \'ten\' with \'ten\' if false (exists)'       => [
                'ten',
                'ten',
                new class extends BasePredicate {
                    public function test(mixed $value): bool
                    {
                        return false;
                    }
                },
                false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider replaceIfElementProvider
     */
    public function canReplaceElementsIfTheyPassPredicate(string $key, mixed $value, Predicate $predicate, bool $result): void
    {
        $collection    = $this->map->copy();
        $existingValue = $collection->get($key);

        self::assertEquals($result, $collection->replaceIf($key, $value, $predicate));
        self::assertSame($result ? $value : $existingValue, $collection->get($key));
    }
}