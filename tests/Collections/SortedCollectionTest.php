<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\Comparators\CallableComparator;
use Smpl\Collections\Comparators\DefaultComparator;
use Smpl\Collections\Contracts\Comparable;
use Smpl\Collections\Predicates\CallablePredicate;
use Smpl\Collections\SortedCollection;

/**
 * @group sortedCollections
 * @group sorted
 */
class SortedCollectionTest extends TestCase
{
    /**
     * @var \Smpl\Collections\SortedCollection
     */
    private SortedCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new SortedCollection(elements: [2, 1, 5, 6, 4, 3]);
    }

    /**
     * @test
     */
    public function sortedCollectionsAreAutomaticallySorted(): void
    {
        $array = $this->collection->toArray();

        self::assertSame(1, $array[0]);
        self::assertSame(2, $array[1]);
        self::assertSame(3, $array[2]);
        self::assertSame(4, $array[3]);
        self::assertSame(5, $array[4]);
        self::assertSame(6, $array[5]);
    }

    /**
     * @test
     */
    public function sortedCollectionsWithUseDefaultComparatorIfOneNotProvided(): void
    {
        self::assertInstanceOf(DefaultComparator::class, $this->collection->getComparator());
    }

    /**
     * @test
     */
    public function sortedCollectionsCanHaveSpecificComparatorsProvidedDuringConstruction(): void
    {
        $collection = new SortedCollection(
            $this->collection,
            new CallableComparator(static function(mixed $a, mixed $b) {
                $aOdd = ($a % 2) === 0;
                $bOdd = ($b % 2) === 0;

                if ($aOdd && $bOdd) {
                    return Comparable::EQUIVALENT;
                }

                if ($aOdd && ! $bOdd) {
                    return Comparable::LESS_THAN;
                }

                return Comparable::GREATER_THAN;
            })
        );
        $array = $collection->toArray();

        self::assertSame(2, $array[0]);
        self::assertSame(4, $array[1]);
        self::assertSame(6, $array[2]);
        self::assertSame(1, $array[3]);
        self::assertSame(3, $array[4]);
        self::assertSame(5, $array[5]);
        self::assertInstanceOf(CallableComparator::class, $collection->getComparator());
    }

    /**
     * @test
     */
    public function sortedCollectionsCanBeCreatedAsEmptyCollections(): void
    {
        $collection = new SortedCollection();

        self::assertCount(0, $collection);
        self::assertSame(0, $collection->count());
        self::assertTrue($collection->isEmpty());
    }

    /**
     * @test
     */
    public function sortedCollectionsCorrectlyCheckIfTheyContainValues(): void
    {
        self::assertTrue($this->collection->contains(1));
        self::assertTrue($this->collection->contains(2));
        self::assertTrue($this->collection->contains(3));
        self::assertTrue($this->collection->contains(4));
        self::assertTrue($this->collection->contains(5));
        self::assertTrue($this->collection->contains(6));

        self::assertFalse($this->collection->contains(7));
        self::assertFalse($this->collection->contains(8));
        self::assertFalse($this->collection->contains(9));
        self::assertFalse($this->collection->contains(10));
        self::assertFalse($this->collection->contains(11));
        self::assertFalse($this->collection->contains(12));
    }

    /**
     * @test
     */
    public function sortedCollectionsCorrectlyCheckIfTheyContainMultipleValues(): void
    {
        self::assertTrue($this->collection->containsAll([1, 2, 3]));
        self::assertTrue($this->collection->containsAll([4, 5, 6]));
        self::assertTrue($this->collection->containsAll([2, 4, 6]));
        self::assertTrue($this->collection->containsAll([1, 3, 5]));

        self::assertFalse($this->collection->containsAll([7, 8, 9]));
        self::assertFalse($this->collection->containsAll([10, 11, 12]));
        self::assertFalse($this->collection->containsAll([1, 0, 2]));
        self::assertFalse($this->collection->containsAll([1, 2, 3, 4, 5, 6, 7]));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanBeCheckedIfTheyAreEmpty(): void
    {
        self::assertTrue((new Collection())->isEmpty());
        self::assertFalse($this->collection->isEmpty());
    }

    /**
     * @test
     */
    public function sortedCollectionsCanBeTurnedIntoArrays(): void
    {
        $array = $this->collection->toArray();

        self::assertCount($this->collection->count(), $array);
        self::assertTrue($this->collection->containsAll($array));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanBeCopied(): void
    {
        $copy = $this->collection->copy();

        self::assertNotSame($this->collection, $copy);
        self::assertCount($this->collection->count(), $copy);
        self::assertTrue($this->collection->containsAll($copy));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanHaveElementsAdded(): void
    {
        $copy = $this->collection->copy();

        self::assertCount(6, $copy);
        self::assertFalse($copy->contains(7));

        $copy->add(7);

        self::assertCount(7, $copy);
        self::assertTrue($copy->contains(7));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanHaveMultipleElementsAdded(): void
    {
        $copy     = $this->collection->copy();
        $elements = [7, 8, 9, 10, 11, 12];

        self::assertCount(6, $copy);
        self::assertFalse($copy->containsAll($elements));

        $copy->addAll($elements);

        self::assertCount(12, $copy);
        self::assertTrue($copy->containsAll($elements));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanBeCleared(): void
    {
        $copy = $this->collection->copy();

        self::assertCount(6, $copy);
        self::assertTrue($copy->contains(3));

        self::assertTrue($copy->remove(3));

        self::assertCount(5, $copy);
        self::assertFalse($copy->contains(3));

        self::assertFalse($copy->remove(3));

        self::assertCount(5, $copy);
        self::assertFalse($copy->contains(3));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanHaveElementsRemoved(): void
    {
        $copy = $this->collection->copy();

        self::assertCount(6, $copy);
        self::assertFalse($copy->isEmpty());

        $copy->clear();

        self::assertCount(0, $copy);
        self::assertTrue($copy->isEmpty());
    }

    /**
     * @test
     */
    public function sortedCollectionsCanHaveMultipleElementsRemoved(): void
    {
        $copy          = $this->collection->copy();
        $elements      = [1, 3, 5];
        $otherElements = [2, 4, 6];

        self::assertCount(6, $copy);
        self::assertTrue($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));

        self::assertTrue($copy->removeAll($elements));

        self::assertCount(3, $copy);
        self::assertFalse($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));

        self::assertFalse($copy->removeAll($elements));

        self::assertCount(3, $copy);
        self::assertFalse($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanRemoveElementsBasedOnAPredicate(): void
    {
        $copy          = $this->collection->copy();
        $elements      = [1, 3, 5];
        $otherElements = [2, 4, 6];
        $predicate     = new CallablePredicate(fn(int $e) => ($e % 2) !== 0);

        self::assertCount(6, $copy);
        self::assertTrue($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));

        self::assertTrue($copy->removeIf($predicate));

        self::assertCount(3, $copy);
        self::assertFalse($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));

        self::assertFalse($copy->removeIf($predicate));

        self::assertCount(3, $copy);
        self::assertFalse($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));
    }

    /**
     * @test
     */
    public function sortedCollectionsCanRetainMultipleElements(): void
    {
        $copy          = $this->collection->copy();
        $elements      = [1, 3, 5];
        $otherElements = [2, 4, 6];

        self::assertCount(6, $copy);
        self::assertTrue($copy->containsAll($elements));
        self::assertTrue($copy->containsAll($otherElements));

        self::assertTrue($copy->retainAll($elements));

        self::assertCount(3, $copy);
        self::assertTrue($copy->containsAll($elements));
        self::assertFalse($copy->containsAll($otherElements));

        self::assertFalse($copy->retainAll($elements));

        self::assertCount(3, $copy);
        self::assertTrue($copy->containsAll($elements));
        self::assertFalse($copy->containsAll($otherElements));
    }


    /**
     * @test
     */
    public function sortedCollectionsAreIterable(): void
    {
        self::assertIsIterable($this->collection);
        self::assertInstanceOf(\IteratorAggregate::class, $this->collection);
        self::assertInstanceOf(\ArrayIterator::class, $this->collection->getIterator());
    }


    /**
     * @test
     */
    public function sortedCollectionsAreCountable(): void
    {
        self::assertCount(6, $this->collection);
        self::assertInstanceOf(\Countable::class, $this->collection);
    }
}