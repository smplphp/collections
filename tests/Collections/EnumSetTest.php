<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\EnumSet;
use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\Tests\Fixtures\TestEnum1;
use Smpl\Collections\Tests\Fixtures\TestEnum2;
use Smpl\Utils\Contracts\Comparator;
use UnitEnum;

/**
 * @group mutable
 * @group set
 * @group enum
 */
class EnumSetTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $elements = [];

    /**
     * @var \Smpl\Collections\EnumSet
     */
    private EnumSet $set;

    public function setUp(): void
    {
        $this->elements = [TestEnum1::One, TestEnum1::Two, TestEnum1::Three, TestEnum1::Four];
        $this->set      = new EnumSet(TestEnum1::class, $this->elements);
    }

    public function setCreatesFromIterables(): array
    {
        return [
            'From array'         => [[TestEnum1::One, TestEnum1::Four], 2],
            'From ArrayIterator' => [new ArrayIterator([TestEnum1::One, TestEnum1::Four]), 2],
            'From Collection'    => [new Collection([TestEnum1::One, TestEnum1::Four]), 2],
        ];
    }

    /**
     * @test
     * @dataProvider setCreatesFromIterables
     */
    public function createsFromIterables(iterable $elements, int $count): void
    {
        $collection = new EnumSet(TestEnum1::class, $elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     * @dataProvider setCreatesFromIterables
     */
    public function createsUsingOfMethod(iterable $elements, int $count): void
    {
        $collection = EnumSet::of(...$elements);

        self::assertCount($count, $collection);
        self::assertTrue($collection->containsAll($elements));
    }

    /**
     * @test
     */
    public function cannotUseOfForMixedEnumTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Cannot create a new instance of EnumSet for elements that are not all of the same enum type, expecting %s',
            TestEnum1::class
        ));

        EnumSet::of(TestEnum1::Two, TestEnum2::One);
    }

    /**
     * @test
     */
    public function canCreateForEnumClass(): void
    {
        $set = EnumSet::noneOf(TestEnum1::class);

        self::assertCount(0, $set);
        self::assertEquals(TestEnum1::class, $set->getEnumClass());
    }

    /**
     * @test
     */
    public function cannotCreateForNonEnumClasses(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create a new instance of EnumSet without providing the enum class');

        EnumSet::noneOf(Collection::class);
    }

    /**
     * @test
     */
    public function canCreateForAllCasesOfAnEnum(): void
    {
        $set = EnumSet::allOf(TestEnum1::class);

        self::assertCount(6, $set);
        self::assertSame(TestEnum1::cases(), $set->toArray());
        self::assertEquals(TestEnum1::class, $set->getEnumClass());
    }

    /**
     * @test
     */
    public function cannotCreateForAllCasesOfNotAnEnum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create a new instance of EnumSet without providing the enum class');

        EnumSet::allOf(Collection::class);
    }

    /**
     * @test
     */
    public function cannotUseOfWithoutElement(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create a new instance of EnumSet without providing the enum class');

        EnumSet::of();
    }

    /**
     * @test
     */
    public function usesSimpleIterator(): void
    {
        $iterator = $this->set->getIterator();

        self::assertInstanceOf(SimpleIterator::class, $iterator);
    }

    /**
     * @test
     */
    public function emptyCollectionsReturnEarly(): void
    {
        $collection = new EnumSet(TestEnum1::class);

        self::assertFalse($collection->contains(TestEnum1::One));
        self::assertFalse($collection->containsAll([TestEnum1::One]));
        self::assertEquals(0, $collection->countOf(TestEnum1::One));
    }

    public function setContainsProvider(): array
    {
        return [
            'Contains TestEnum1::One'   => [TestEnum1::One, true],
            'Contains TestEnum1::Two'   => [TestEnum1::Two, true],
            'Contains TestEnum1::Three' => [TestEnum1::Three, true],
            'Contains TestEnum1::Four'  => [TestEnum1::Four, true],
            'Contains TestEnum2::One'   => [TestEnum2::One, false],
            'Contains TestEnum2::Two'   => [TestEnum2::Two, false],
            'Contains TestEnum2::Three' => [TestEnum2::Three, false],
            'Contains TestEnum2::Four'  => [TestEnum2::Four, false],
        ];
    }

    /**
     * @test
     * @dataProvider setContainsProvider
     */
    public function knowsWhatItContains(UnitEnum $value, bool $result): void
    {
        self::assertEquals($result, $this->set->contains($value));
    }

    public function setContainsAllProvider(): array
    {
        return [
            'Contains [TestEnum1::One, TestEnum1::Three]' => [[TestEnum1::One, TestEnum1::Three], true],
            'Contains [TestEnum1::Two, TestEnum1::Four]'  => [[TestEnum1::Two, TestEnum1::Four], true],
            'Contains [TestEnum2::One, TestEnum2::Three]' => [[TestEnum2::One, TestEnum2::Three], false],
            'Contains [TestEnum2::Two, TestEnum2::Four]'  => [[TestEnum2::Two, TestEnum2::Four], false],
            'Contains [TestEnum1::One, TestEnum2::Three]' => [[TestEnum1::One, TestEnum2::Three], false],
            'Contains [TestEnum2::Two, TestEnum1::Four]'  => [[TestEnum2::Two, TestEnum1::Four], false],
        ];
    }

    /**
     * @test
     * @dataProvider setContainsAllProvider
     */
    public function knowsWhatItContainsAll(array $value, bool $result): void
    {
        self::assertEquals($result, $this->set->containsAll($value));
    }

    public function setCountOfProvider(): array
    {
        return [
            'Count of TestEnum1::One'   => [TestEnum1::One, 1],
            'Count of TestEnum1::Two'   => [TestEnum1::Two, 1],
            'Count of TestEnum1::Three' => [TestEnum1::Three, 1],
            'Count of TestEnum1::Four'  => [TestEnum1::Four, 1],
            'Count of TestEnum2::One'   => [TestEnum2::One, 0],
            'Count of TestEnum2::Two'   => [TestEnum2::Two, 0],
            'Count of TestEnum2::Three' => [TestEnum2::Three, 0],
            'Count of TestEnum2::Four'  => [TestEnum2::Four, 0],
        ];
    }

    /**
     * @test
     * @dataProvider setCountOfProvider
     */
    public function countsMatchingElements(UnitEnum $value, int $result): void
    {
        self::assertEquals($result, $this->set->contains($value));
    }

    /**
     * @test
     */
    public function createsCopiesOfItself(): void
    {
        $modifiedCopy = $this->set->copy([TestEnum1::One, TestEnum1::Four]);

        self::assertSame($this->set->toArray(), $this->set->copy()->toArray());
        self::assertSame([TestEnum1::One, TestEnum1::Four], $modifiedCopy->toArray());
        self::assertNotSame($this->set->toArray(), $modifiedCopy->toArray());
    }

    /**
     * @test
     */
    public function knowsWhenItIsEmpty(): void
    {
        self::assertTrue((new EnumSet(TestEnum1::class))->isEmpty());
        self::assertFalse($this->set->isEmpty());
    }

    /**
     * @test
     */
    public function canBeConvertedIntoAnArrayList(): void
    {
        $elements = $this->set->toArray();

        self::assertSame(count($elements), $this->set->count());
        self::assertTrue(array_is_list($elements));
    }

    public function addElementProvider(): array
    {
        return [
            'Add TestEnum1::Five' => [TestEnum1::Five, true, true, 1],
            'Add TestEnum1::One'  => [TestEnum1::One, false, true, 1],
        ];
    }

    /**
     * @test
     * @dataProvider addElementProvider
     */
    public function canAddElements(UnitEnum $value, bool $result, bool $contains, int $count): void
    {
        $collection = $this->set->copy();

        self::assertEquals($result, $collection->add($value));
        self::assertEquals($contains, $collection->contains($value));
        self::assertEquals($count, $collection->countOf($value));
    }

    public function addElementExceptionProvider(): array
    {
        return [
            'Add TestEnum2::Four' => [TestEnum2::Four],
            'Add TestEnum2::One'  => [TestEnum2::One],
            'Add \'no\''          => ['no'],
            'Add 11'              => [11],
        ];
    }

    /**
     * @test
     * @dataProvider addElementExceptionProvider
     */
    public function cannotAddElementsThatDontMatchEnumClass(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Cannot add elements not of type \'%s\'',
            TestEnum1::class
        ));

        $this->set->add($value);
    }

    public function addAllElementProvider(): array
    {
        return [
            'Add [TestEnum1::Five, TestEnum1::Six] (new, new, new)'                                    => [[TestEnum1::Five, TestEnum1::Six], true, [true, true, true], [1, 1, 1]],
            'Add [TestEnum1::Five, TestEnum1::Six, TestEnum1::One] (new, new, duplicate)'              => [[TestEnum1::Five, TestEnum1::Six, TestEnum1::One], true, [true, true, true], [1, 1, 1]],
            'Add [TestEnum1::One, TestEnum1::Two, TestEnum1::Three] (duplicate, duplicate, duplicate)' => [[TestEnum1::One, TestEnum1::Two, TestEnum1::Three], false, [true, true, true], [1, 1, 1]],
        ];
    }

    /**
     * @test
     * @dataProvider addAllElementProvider
     */
    public function canAddAllElements(array $value, bool $result, array $contains, array $count): void
    {
        $collection = $this->set->copy();

        self::assertEquals($result, $collection->addAll($value));

        foreach ($value as $i => $item) {
            self::assertEquals($contains[$i], $collection->contains($item));
            self::assertEquals($count[$i], $collection->countOf($item));
        }
    }

    public function addAllElementExceptionProvider(): array
    {
        return [
            'Add [TestEnum2::One, TestEnum2::Four]'  => [[TestEnum2::One, TestEnum2::Four]],
            'Add [TestEnum12::One, TestEnum1::Five]' => [[TestEnum2::One, TestEnum1::Five]],
        ];
    }

    /**
     * @test
     * @dataProvider addAllElementExceptionProvider
     */
    public function cannotAddAllElementsThatDontMatchEnumClass(array $values): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Cannot add elements not of type \'%s\'',
            TestEnum1::class
        ));

        $this->set->addAll($values);
    }

    /**
     * @test
     */
    public function canBeCleared(): void
    {
        $collection = $this->set->copy();

        self::assertCount(4, $collection);

        $empty = $collection->clear();

        self::assertCount(0, $empty);
        self::assertTrue($empty->isEmpty());
        self::assertEmpty($empty->toArray());
    }

    public function removeElementProvider(): array
    {
        return [
            'Remove TestEnum1::Five (new)'    => [TestEnum1::Five, false, false, 0, false, 0],
            'Remove TestEnum1::Six (new)'     => [TestEnum1::Six, false, false, 0, false, 0],
            'Remove TestEnum1::One (exists)'  => [TestEnum1::One, true, false, 0, true, 1],
            'Remove TestEnum1::Two (exists)'  => [TestEnum1::Two, true, false, 0, true, 1],
            'Remove TestEnum2::One (wrong)'   => [TestEnum2::One, false, false, 0, false, 0],
            'Remove TestEnum2::Two (wrong)'   => [TestEnum2::Two, false, false, 0, false, 0],
            'Remove TestEnum2::Three (wrong)' => [TestEnum2::Three, false, false, 0, false, 0],
        ];
    }

    /**
     * @test
     * @dataProvider removeElementProvider
     */
    public function canRemoveElements(UnitEnum $value, bool $result, bool $contains, int $count, bool $preContains, int $preCount, array $add = [], ?Comparator $comparator = null): void
    {
        $collection = $this->set->copy();

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
            'Remove [TestEnum1::Five, TestEnum1::Six] (new, new)'                                                         => [[TestEnum1::Five, TestEnum1::Six], false, 4, 4],
            'Remove [TestEnum1::One, TestEnum1::Two, TestEnum1::Five] (exists, exists, new)'                              => [[TestEnum1::One, TestEnum1::Two, TestEnum1::Five], true, 4, 2],
            'Remove [TestEnum1::One, TestEnum1::Two, TestEnum1::Three] (exists, exists, exists)'                          => [[TestEnum1::One, TestEnum1::Two, TestEnum1::Three], true, 4, 1],
            'Remove [TestEnum1::One, TestEnum1::Two, TestEnum1::Three, TestEnum1::Four] (exists, exists, exists, exists)' => [[TestEnum1::One, TestEnum1::Two, TestEnum1::Three, TestEnum1::Four],
                                                                                                                              true,
                                                                                                                              4,
                                                                                                                              0],
        ];
    }

    /**
     * @test
     * @dataProvider removeAllElementProvider
     */
    public function canRemoveAllElements(array $value, bool $result, int $preCount, int $postCount, array $add = []): void
    {
        $collection = $this->set->copy();

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

    public function retainAllElementProvider(): array
    {
        return [
            'Retain [TestEnum1::Five, TestEnum1::Six] (new, new)'                                => [[TestEnum1::Five, TestEnum1::Six], true, 4, 0, [], [false, false], [0, 0]],
            'Retain [TestEnum1::Three, TestEnum1::Four, TestEnum1::Five] (exists, exists, new)'  => [[TestEnum1::Three, TestEnum1::Four, TestEnum1::Five],
                                                                                                     true,
                                                                                                     4,
                                                                                                     2,
                                                                                                     [],
                                                                                                     [true, true, false],
                                                                                                     [1, 1, 0]],
            'Retain [TestEnum1::One, TestEnum1::Two, TestEnum1::Three] (exists, exists, exists)' => [[TestEnum1::One, TestEnum1::Two, TestEnum1::Three], true, 4, 3, [], [true, true, true]],
            'Retain [TestEnum2::One, TestEnum2::Two, TestEnum2::Three] (wrong, wrong, wrong)'    => [[TestEnum2::One, TestEnum2::Two, TestEnum2::Three],
                                                                                                     false,
                                                                                                     4,
                                                                                                     4,
                                                                                                     [],
                                                                                                     [false, false, false],
                                                                                                     [0, 0, 0]],
            'Retain [TestEnum2::One, TestEnum2::Two, TestEnum1::Three] (wrong, wrong, exists)'    => [[TestEnum2::One, TestEnum2::Two, TestEnum1::Three],
                                                                                                     true,
                                                                                                     4,
                                                                                                     1,
                                                                                                     [],
                                                                                                     [false, false, true],
                                                                                                     [0, 0, 1]],
        ];
    }

    /**
     * @test
     * @dataProvider retainAllElementProvider
     */
    public function canRetainAllElements(array $value, bool $result, int $preCount, int $postCount, array $add = [], array $contains = [], array $count = []): void
    {
        $collection = $this->set->copy();

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
}