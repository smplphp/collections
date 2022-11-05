<?php

namespace Smpl\Collections\Contracts;

use Smpl\Utils\Contracts\Predicate;

/**
 * Collection Contract
 *
 * This is the root contract in the collection hierarchy, forming the base
 * functionality of all collections. Basic collections will implement
 * this contract directly, whereas others will implement it through subinterfaces.
 *
 * Collections represent groups of objects, known as elements of type E indexed
 * by indexes/keys of type I. The type I will almost always be int, but this
 * exists here for maps and other specific collections that do not index
 * by integer.
 *
 * @template I of array-key
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Enumerable<I, E>
 */
interface Collection extends Enumerable
{
    /**
     * Create a new instance of this collection for the provided elements.
     *
     * This method is a variadic static constructor for the collection,
     * creating a new instance for all the provided elements.
     *
     * @template NE of mixed
     *
     * @param NE ...$elements
     *
     * @return static
     */
    public static function of(mixed ...$elements): static;

    /**
     * Ensure that this collection contains the provided element.
     *
     * This method will ensure that the collection contains the provided element,
     * returning true if the collection was modified. In the case of the
     * implementor not allowing duplicates, this method will return false.
     *
     * If the element provided cannot be added to the collection for a reason
     * other than it being duplicate, such as it being null, the implementor
     * must throw a {@see \Smpl\Collections\Exceptions\InvalidArgumentException}
     * exception.
     *
     * @param E $element
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public function add(mixed $element): bool;

    /**
     * Ensure that this collection contains all the provided elements.
     *
     * This method will function exactly like
     * {@see \Smpl\Collections\Contracts\MutableCollection::add()} except that it
     * deals with multiple elements, rather than just one.
     *
     * Because of this, it is possible for this method to return true, even if
     * only one of the provided elements are actually added to this collection.
     *
     * This method must also throw a {@see \Smpl\Collections\Exceptions\InvalidArgumentException}
     * if any of the provided elements cannot be added to the collection for
     * reasons other than being a duplicate.
     *
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public function addAll(iterable $elements): bool;

    /**
     * Clear this collection of all elements.
     *
     * This method will remove all elements from this collection, resetting
     * to an empty collection such that {@see \Smpl\Collections\Contracts\Collection::isEmpty()}
     * returns false and {@see \Smpl\Collections\Contracts\Collection::count()}
     * returns 0.
     *
     * @return static
     */
    public function clear(): static;

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
     * Remove the provided element from this collection.
     *
     * This method will remove the provided element from the collection,
     * return true if the collection was modified, false otherwise.
     *
     * The exact method for determining whether an element should be removed
     * will depend entirely on the implementation.
     *
     * @param E $element
     *
     * @return bool
     */
    public function remove(mixed $element): bool;

    /**
     * Remove all provided elements from this collection.
     *
     * This method will remove all elements from the collection that are also
     * contained within the provided elements, returning true if the collection
     * was modified, false otherwise, functioning like
     * {@see \Smpl\Collections\Contracts\MutableCollection::remove()}, but for
     * multiple elements.
     *
     * Because of this, it is possible for this method to return true, even if
     * only one of the provided elements were actually removed from this collection.
     *
     * The exact method for determining whether an element should be removed
     * will depend entirely on the implementation.
     *
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function removeAll(iterable $elements): bool;

    /**
     * Remove all elements from this collection that pass the provided filter.
     *
     * This method will remove all elements from this collection that pass
     * the provided filter, returning true if this collection was modified,
     * false otherwise.
     *
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return bool
     */
    public function removeIf(Predicate $filter): bool;

    /**
     * Remove all elements not in the provided elements.
     *
     * This method will function as the opposite of
     * {@see \Smpl\Collections\Contracts\MutableCollection::removeAll()}, removing
     * all but the elements provided by $elements. This method will return true
     * if the collection was modified, false otherwise.
     *
     * It is possible for this method to return true, even if only one element is
     * removed from this collection.
     *
     * If the provided elements contain elements not present in the collection,
     * they will not be added.
     *
     * The exact method for determining whether an element should be removed
     * will depend entirely on the implementation.
     *
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function retainAll(iterable $elements): bool;
}