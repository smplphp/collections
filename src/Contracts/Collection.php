<?php

namespace Smpl\Collections\Contracts;

/**
 * Collection Contract
 *
 * This is the root contract in the collection hierarchy, forming the base
 * immutable functionality of all collections. Basic collections will implement
 * this contract directly, whereas others will implement it through subinterfaces.
 *
 * Collections represent groups of objects, known as elements of type E indexed
 * by indexes/keys of type I. The type I will almost always be int, but this
 * exists here for maps and other specific collections that do not index
 * by integer.
 *
 * @template I of mixed
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Enumerable<I, E>
 */
interface Collection extends Enumerable
{
    /**
     * Check if this collection contains the provided element.
     *
     * This method should return true if the provided element exists within
     * the collection, but the exact method of determining this will be down
     * to the individual implementation.
     *
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool;

    /**
     * Check if this collection contains all provided elements.
     *
     * This method should return true if all the provided elements exist within
     * the collection, but the exact method of determining this will be down
     * to the individual implementation.
     *
     * Should any of the provided elements not be found in the collection,
     * this method should return false.
     *
     * If the $comparator argument is provided, the implementor must use it to
     * determine the equality of elements.
     *
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool;

    /**
     * Create a copy of this collection.
     *
     * This method should create a copy of the collection, not a clone, such that
     * the two collections will not be identical, but may be equal.
     *
     * If the $elements argument is provided, whether empty or not, the copy
     * of the collection should be populated with those elements.
     *
     * @template     NE of mixed
     *
     * @param iterable<NE>|null $elements
     *
     * @return static
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function copy(iterable $elements = null): static;

    /**
     * Check if this collection is empty.
     *
     * This method will return true if this collection is empty, and false
     * otherwise.
     *
     * Implementors must ensure that if this method returns true, calls to
     * {@see \Smpl\Collections\Contracts\Collection::count()} before the
     * collection is modified must return exactly 0.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Get this collections' comparator.
     *
     * Get the comparator that this collection uses to compare elements. Some
     * implementations will fall back to a default implementation, whereas
     * others will have their own logic for comparing values.
     *
     * This method is analogous with {@see \Smpl\Collections\Contracts\ComparesValues::getComparator()},
     * but this contract does not extend that one, because the comparator
     * cannot be changed once the collection has been created.
     *
     * @return \Smpl\Collections\Contracts\Comparator<E>|null
     *
     * @see \Smpl\Collections\Contracts\ComparesValues::getComparator()
     */
    public function getComparator(): ?Comparator;
}