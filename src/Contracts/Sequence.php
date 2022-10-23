<?php

namespace Smpl\Collections\Contracts;

use ArrayAccess;

/**
 * Sequence Contract
 *
 * This contract represents a collection of elements in a list-like
 * structure where each element's order, its sequence, is important.
 *
 * A sequences indexes should always start at 0, and end at (count - 1).
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Collection<int, E>
 * @extends \ArrayAccess<int, E>
 */
interface Sequence extends Collection, ArrayAccess
{
    /**
     * Get the index for the provided element, starting from the provided index.
     *
     * This method will return the index for the provided element, where the
     * index is greater than or equal to $index.
     *
     * If $index is outside the index range for this collection, a
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException} will be thrown.
     *
     * @param E          $element
     * @param int<0,max> $index
     *
     * @return int<0,max>|null
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function find(mixed $element, int $index): ?int;

    /**
     * Get the first element in this collection.
     *
     * This method will return the first element, or the "head" of this
     * collection, possibly returning null of it is empty. This method is the
     * opposite of {@see \Smpl\Collections\Contracts\Sequence::last()}.
     *
     * @return E|null
     */
    public function first(): mixed;

    /**
     * Get the element at the given index.
     *
     * This method will return the element present at the provided index. If
     * $index is outside the index range for this collection, a
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException} will be thrown.
     *
     * @param int<0, max> $index
     *
     * @return E|null
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function get(int $index): mixed;

    /**
     * Check if this collection covers the provided index.
     *
     * This method will return true if the provided index is covered by this
     * sequence, otherwise it will return false.
     *
     * This method exists as a simple check that avoid dealing with
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException}.
     *
     * @param int $index
     *
     * @return bool
     */
    public function has(int $index): bool;

    /**
     * Get the first index for the specified element.
     *
     * This method will return the index for the first occurrence of the provided
     * element, using the collections' comparator, if one is set.
     *
     * @param E $element
     *
     * @return int<0,max>|null
     */
    public function indexOf(mixed $element): ?int;

    /**
     * Get all indexes for the specified element.
     *
     * This method will return all indexes for the provided element, using the
     * collections comparator if one is present.
     *
     * @param E $element
     *
     * @return \Smpl\Collections\Contracts\Set<int>
     */
    public function indexesOf(mixed $element): Set;

    /**
     * Get the last index for the specified element.
     *
     * This method will return the index for the last occurrence of the provided
     * element, using the collections' comparator, if one is set.
     *
     * @param E $element
     *
     * @return int<0,max>|null
     */
    public function lastIndexOf(mixed $element): ?int;

    /**
     * Get the last element in this collection.
     *
     * This method will return the last element of this collection, possibly
     * returning null of it is empty. This method is the opposite of
     * {@see \Smpl\Collections\Contracts\Sequence::first()}.
     *
     * @return E|null
     */
    public function last(): mixed;

    /**
     * Put the element at the provided index in this collection.
     *
     * This method will put the provided element at the provided index,
     * incrementing the index of all elements with an index of greater than or
     * equal to $index.
     *
     * If $index is greater than this collections max index plus one,
     * or less than 0, a {@see \Smpl\Collections\Exceptions\OutOfRangeException}
     * will be thrown.
     *
     * @param int<0, max> $index
     * @param E           $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function put(int $index, mixed $element): static;

    /**
     * Put all the elements at the provided index in this collection.
     *
     * This method will put all the provided elements into this collection,
     * starting at index $index, incrementing the index of all elements with
     * an index of greater than or equal to $index.
     *
     * If $index is greater than this collections max index plus one,
     * or less than 0, a {@see \Smpl\Collections\Exceptions\OutOfRangeException}
     * will be thrown.
     *
     * If count($elements) is greater than the max index of this collection, the
     * additional elements will be added onto the end, effectively increasing
     * the size of this collection.
     *
     * @param int<0, max> $index
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function putAll(int $index, iterable $elements): static;

    /**
     * Set the element at the provided index in this collection.
     *
     * This method will put the provided element at the provided index,
     * replacing any element already present.
     *
     * If $index is greater than this collections max index plus one,
     * or less than 0, a {@see \Smpl\Collections\Exceptions\OutOfRangeException}
     * will be thrown.
     *
     * @param int<0, max> $index
     * @param E           $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function set(int $index, mixed $element): static;

    /**
     * Set all the elements at the provided index in this collection.
     *
     * This method will put all the provided elements into this collection,
     * starting at index $index, replacing any elements with an index of
     * greater than or equal to $index but less than $index + (count($elements) - 1).
     *
     * If $index is greater than this collections max index plus one,
     * or less than 0, a {@see \Smpl\Collections\Exceptions\OutOfRangeException}
     * will be thrown.
     *
     * If count($elements) is greater than the max index of this collection, the
     * additional elements will be added onto the end, effectively increasing
     * the size of this collection.
     *
     * @param int<0, max> $index
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function setAll(int $index, iterable $elements): static;

    /**
     * Get a subset of this collections elements.
     *
     * This method will return a new collection containing a subset of the
     * elements within this collection, starting with the index provided by
     * $index (inclusive).
     *
     * If $index is outside the index range for this collection, a
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException} will be thrown.
     *
     * If $length is a positive integer, the new collection will contain
     * elements from the range of $index to ($index + $length), providing that
     * ($index + $length) is less than (count - 1), otherwise an
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException} will be thrown.
     *
     * If $length is a negative integer, the new collection will contain
     * elements from the range of $index to ((count - 1) - $length), providing
     * that ((count - 1) - $length) is not less than 0, and is greater than
     * $index, otherwise an
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException} will be thrown.
     *
     * If $length is null, the new collection will contain every element that
     * succeeds the element at index $index.
     *
     * @param int<0, max> $index
     * @param int|null    $length
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function subset(int $index, int $length = null): static;

    /**
     * Get all elements from this collection, besides the first.
     *
     * This method returns the "tail" of the collection, being every element but
     * the first.
     *
     * Implementations of this method should function the same as calling
     * {@see \Smpl\Collections\Contracts\Sequence::subset()}, with an index
     * of 1.
     *
     * @return static
     */
    public function tail(): static;

    /**
     * Remove an element from this collection by its index.
     *
     * This method will remove an element from the collection using its index.
     * All elements with an index > $index will have their index decremented
     * by one.
     *
     * If $index is outside the index range for this collection, a
     * {@see \Smpl\Collections\Exceptions\OutOfRangeException} will be thrown.
     *
     * @param int<0,max> $index
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function unset(int $index): static;
}