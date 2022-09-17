<?php

namespace Smpl\Collections\Contracts;

/**
 * Collection Mutable Contract
 *
 * This contract provides the mutable functionality for the base collection as
 * defined in {@see \Smpl\Collections\Contracts\Collection}.
 *
 * @template E of mixed
 * @template-extends \Smpl\Collections\Contracts\Collection<E>
 */
interface CollectionMutable extends Collection
{
    /**
     * Add an element to the collection.
     *
     * @param E $element
     *
     * @return static
     */
    public function add(mixed $element): static;

    /**
     * Add all the provided elements to the collection.
     *
     * @param iterable<E> $elements
     *
     * @return static
     */
    public function addAll(iterable $elements): static;

    /**
     * Clear all elements from the collection.
     *
     * @return static
     */
    public function clear(): static;

    /**
     * Remove an element from the collection.
     *
     * @param E $element
     *
     * @return bool Returns true if the collection was changed.
     */
    public function remove(mixed $element): bool;

    /**
     * Remove all the provided elements from the collection.
     *
     * @param iterable<E> $elements
     *
     * @return bool Returns true if the collection was changed.
     */
    public function removeAll(iterable $elements): bool;

    /**
     * Remove all elements that do not pass the provided criteria.
     *
     * @param \Smpl\Collections\Contracts\Predicate<E>|callable(E $element):bool $criteria
     *
     * @return bool Returns true if the collection was changed.
     */
    public function removeIf(Predicate|callable $criteria): bool;

    /**
     * Retain all elements in the collection that also exist in the provided collection,
     * removing any that don't.
     *
     * @param iterable<E> $elements
     *
     * @return bool Returns true if the collection was changed.
     */
    public function retainAll(iterable $elements): bool;

    /**
     * Create a copy of the collection.
     *
     * @return static<E>
     */
    public function copy(): static;
}