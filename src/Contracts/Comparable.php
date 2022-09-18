<?php

namespace Smpl\Collections\Contracts;

/**
 * Comparable Contracts
 *
 * Should be used on classes that can be compared to determine their order for
 * the purpose of sorting.
 *
 * @template V of mixed
 */
interface Comparable
{
    /**
     * Ideal result when a compared value is less than.
     */
    public const LESS_THAN = -1;

    /**
     * Ideal result when a compared value is equivalent.
     */
    public const EQUIVALENT = 0;

    /**
     * Ideal result when a compared value is less than.
     */
    public const GREATER_THAN = +1;

    /**
     * Compare the implementor to the provided value.
     *
     * This method compares the provided value against the implementor, returning
     * a negative integer (ideally -1) if the value is less than, a zero (0) if
     * the value is equivalent to, or a positive integer (ideally +1)  if the
     * value is greater than the implementor.
     *
     * If the provided value is null, or another type that is not comparable
     * to the implementor, this method can throw an {@see \InvalidArgumentException}.
     * The choice to do this will depend entirely on the implementation.
     *
     * @param V $value
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\Comparable::EQUIVALENT
     * @see \Smpl\Collections\Contracts\Comparable::GREATER_THAN
     * @see \Smpl\Collections\Contracts\Comparable::LESS_THAN
     */
    public function compareTo(mixed $value): int;
}