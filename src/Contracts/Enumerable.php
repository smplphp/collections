<?php
declare(strict_types=1);

namespace Smpl\Collections\Contracts;

use Countable;
use IteratorAggregate;

/**
 * Enumerable Contract
 *
 * The enumerable contract provides baseline functionality for collections that
 * make them enumerable, ie, countable and iterable.
 *
 * @template-covariant  I of array-key
 * @template            E of mixed
 * @extends  \IteratorAggregate<I, E>
 */
interface Enumerable extends Countable, IteratorAggregate
{
    /**
     * Get the number of elements within this collection.
     *
     * Returns the current total number of elements contained within this
     * collection.
     *
     * Implementors must ensure that if this method returns 0, calls to
     * {@see \Smpl\Collections\Contracts\Collection::isEmpty()} before the
     * collection is modified must return true.
     *
     * @return int<0, max>
     */
    public function count(): int;

    /**
     * Get the number of elements equal to the provided element within this
     * collection.
     *
     * This method is similar to {@see \Smpl\Collections\Contracts\Collection::contains()}
     * except that rather than just true or false of the element is present, it
     * should return the total number of times the element was found within
     * the collection.
     *
     * If the $comparator argument is provided, the implementor must use it to
     * determine the equality of elements.
     *
     * @param E $element
     *
     * @return int<0, max>
     */
    public function countOf(mixed $element): int;

    /**
     * Get an array representation of the elements in this collection.
     *
     * This method creates an array based on the elements stored in the collection.
     *
     * The approach to this will be implementation specific, and because of this
     * it cannot be guaranteed that the indexes/keys in the returned array
     * represent the indexes/keys for the same elements while in the collection.
     *
     * Implementors must ensure that an empty collection that returns 0 for
     * {@see \Smpl\Collections\Contracts\Collection::count()} and true for
     * {@see \Smpl\Collections\Contracts\Collection::isEmpty()} must return an
     * empty array.
     *
     * @return list<E>|array<I, E>
     */
    public function toArray(): array;
}