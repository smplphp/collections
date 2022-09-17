<?php

namespace Smpl\Collections\Contracts;

use Countable;
use IteratorAggregate;

/**
 * Collection Contract
 *
 * The base contract for all collections. This contract is provides no mutability,
 * which is instead provided by {@see \Smpl\Collections\Contracts\CollectionMutable}.
 *
 * @template E of mixed
 */
interface Collection extends IteratorAggregate, Countable
{
    /**
     * Check if the collection contains the provided element.
     *
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool;

    /**
     * Check if the collection contains all the provided elements.
     *
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool;

    /**
     * Check if the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Get the number of elements in the collection.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Get an array representation of this collection.
     *
     * @return list<E>
     */
    public function toArray(): array;
}