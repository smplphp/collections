<?php

namespace Smpl\Collections\Contracts;

/**
 * Comparator Contract
 *
 * Comparators provide a way to compare multiple elements against each other, with
 * the intention of providing a sort order.
 *
 * @see \Smpl\Collections\Contracts\Sortable
 *
 * @template V of mixed
 */
interface Comparator
{
    /**
     * Compare two values to determine their sorting order relative to each other.
     *
     * This method compares the provided values against each other, returning
     * a negative integer (ideally -1) if $a is less than $b, a zero (0) if
     * $a is equivalent to $b, or a positive integer (ideally +1) if $a is greater
     * than the $b.
     *
     * If a provided value is null, or the provided types are not comparable,
     * this method can throw an {@see \InvalidArgumentException}. The choice to do
     * this will depend entirely on the implementation.
     *
     * @param V $a
     * @param V $b
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\Comparable::EQUIVALENT
     * @see \Smpl\Collections\Contracts\Comparable::GREATER_THAN
     * @see \Smpl\Collections\Contracts\Comparable::LESS_THAN
     */
    public function compare(mixed $a, mixed $b): int;

    /**
     * Invokable method to let a comparator be treated as a callable.
     *
     * @param V $a
     * @param V $b
     *
     * @return int
     *
     * @see \Smpl\Collections\Contracts\Comparator::compare()
     */
    public function __invoke(mixed $a, mixed $b): int;
}