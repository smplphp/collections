<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Collections;

use Countable;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\CollectionMutable;
use Smpl\Collections\ImmutableCollection;
use Smpl\Collections\Predicates\CallablePredicate;

/**
 * @group immutableCollections
 * @group immutable
 */
class ImmutableCollectionTest extends TestCase
{
    /**
     * @var \Smpl\Collections\ImmutableCollection
     */
    private ImmutableCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new ImmutableCollection([1, 2, 3, 4, 5, 6]);
    }

    /**
     * @test
     */
    public function immutableCollectionsAreImmutable(): void
    {
        self::assertInstanceOf(Collection::class, $this->collection);
        self::assertNotInstanceOf(CollectionMutable::class, $this->collection);
    }

    /**
     * @test
     */
    public function immutableCollectionsCanBeCreatedAsEmptyCollections(): void
    {
        $collection = new ImmutableCollection();

        self::assertCount(0, $collection);
        self::assertSame(0, $collection->count());
        self::assertTrue($collection->isEmpty());
    }

    /**
     * @test
     */
    public function immutableCollectionsCorrectlyCheckIfTheyContainValues(): void
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
    public function immutableCollectionsCorrectlyCheckIfTheyContainMultipleValues(): void
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
    public function immutableCollectionsCanBeCheckedIfTheyAreEmpty(): void
    {
        self::assertTrue((new ImmutableCollection())->isEmpty());
        self::assertFalse($this->collection->isEmpty());
    }

    /**
     * @test
     */
    public function immutableCollectionsCanBeTurnedIntoArrays(): void
    {
        $array = $this->collection->toArray();

        self::assertCount($this->collection->count(), $array);
        self::assertTrue($this->collection->containsAll($array));
    }


    /**
     * @test
     */
    public function immutableCollectionsAreIterable(): void
    {
        self::assertIsIterable($this->collection);
        self::assertInstanceOf(IteratorAggregate::class, $this->collection);
        self::assertInstanceOf(\ArrayIterator::class, $this->collection->getIterator());
    }


    /**
     * @test
     */
    public function immutableCollectionsAreCountable(): void
    {
        self::assertCount(6, $this->collection);
        self::assertInstanceOf(Countable::class, $this->collection);
    }
}