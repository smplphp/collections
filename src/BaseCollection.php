<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\HasComparator;
use Smpl\Collections\Concerns\NewCollectionOfElements;
use Smpl\Collections\Helpers\IterableHelper;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Helpers\ComparisonHelper;
use Smpl\Utils\Support\Range;
use Traversable;

/**
 * Base Collection
 *
 * This class forms the base for collections, preventing the need to duplicate
 * code and method definitions where inheritance will suffice.
 *
 * @template I of array-key
 * @template E of mixed
 * @implements \Smpl\Collections\Contracts\Collection<I, E>
 */
abstract class BaseCollection implements Contracts\Collection
{
    use HasComparator,
        NewCollectionOfElements;

    /**
     * @var array<I, E>
     */
    protected array $elements = [];

    /**
     * @var int<0, max>
     */
    protected int $count = 0;

    /**
     * @var \Smpl\Utils\Support\Range<int>
     */
    protected Range $range;

    /**
     * @param iterable<E>|null                         $elements
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(iterable $elements = null, ?Comparator $comparator = null)
    {
        $this->range = new Range(0, 0);

        if ($elements !== null) {
            $this->addAll($elements);
        }

        $this->setComparator($comparator);
    }

    /**
     * Set the current count for this collection.
     *
     * This method exists so that the value of {@see \Smpl\Collections\BaseCollection::$count}
     * can be bound within the range of 0 <> {@see PHP_INT_MAX}.
     *
     * @param int $count
     *
     * @return void
     *
     * @infection-ignore-all
     */
    protected function setCount(int $count): void
    {
        $this->count = max(0, $count);
        $this->range->setEnd($this->count);
    }

    /**
     * Modify the current count for this collection.
     *
     * This method exists to provide an easy way to modify the count for this
     * collection, without referencing the actual count itself.
     *
     * @param int $count
     *
     * @return void
     */
    protected function modifyCount(int $count): void
    {
        $this->setCount($this->count() + $count);
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return IterableHelper::contains($this->elements, $element, $this->comparator);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return IterableHelper::containsAll($this->elements, $elements, $this->comparator);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return \Traversable<I, E>
     */
    public function getIterator(): Traversable
    {
        return new SimpleIterator($this->elements);
    }

    /**
     * @return int<0,max>
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @param E $element
     *
     * @return int<0,max>
     */
    public function countOf(mixed $element): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return IterableHelper::countOf($this->elements, $element, $this->comparator);
    }

    /**
     * @return list<E>
     */
    public function toArray(): array
    {
        /**
         * @psalm-suppress RedundantFunctionCall
         * @infection-ignore-all
         */
        return array_values($this->elements);
    }

    /**
     * @param E $element
     *
     * @return bool
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    public function add(mixed $element): bool
    {
        $this->elements[] = $element;
        $this->modifyCount(1);

        return true;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function addAll(iterable $elements): bool
    {
        /** @infection-ignore-all */
        $modified = false;

        foreach ($elements as $element) {
            if ($this->add($element)) {
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @return static
     */
    public function clear(): static
    {
        $this->elements = [];
        /** @infection-ignore-all */
        $this->setCount(0);

        return $this;
    }

    /**
     * Remove an element by its index.
     *
     * This method is a helper message to avoid duplicating removal by index
     * functionality.
     *
     * @param I $index
     *
     * @return void
     *
     * @infection-ignore-all
     */
    protected function removeElementByIndex(mixed $index): void
    {
        unset($this->elements[$index]);
        $this->modifyCount(-1);
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function remove(mixed $element): bool
    {
        $modified   = false;
        $comparator = $this->getComparator() ?? new IdenticalityComparator();

        foreach ($this->elements as $index => $existingElement) {
            if ($comparator->compare($existingElement, $element) === ComparisonHelper::EQUAL_TO) {
                $this->removeElementByIndex($index);
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function removeAll(iterable $elements): bool
    {
        $modified = false;

        foreach ($elements as $element) {
            if ($this->remove($element)) {
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return bool
     */
    public function removeIf(Predicate $filter): bool
    {
        $modified = false;

        foreach ($this->elements as $index => $element) {
            if ($filter->test($element)) {
                $this->removeElementByIndex($index);
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function retainAll(iterable $elements): bool
    {
        $modified   = false;
        $collection = new Collection($elements, $this->getComparator());

        foreach ($this->elements as $element) {
            if (! $collection->contains($element) && $this->remove($element)) {
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * Get this collections max index.
     *
     * This method will return the maximum index for the elements currently
     * stored in this collection.
     *
     * @return int<0, max>
     *
     * @internal
     *
     * @infection-ignore-all
     */
    protected function getMaxIndex(): int
    {
        return max(0, $this->count() - 1);
    }
}