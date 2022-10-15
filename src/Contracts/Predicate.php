<?php

namespace Smpl\Collections\Contracts;

/**
 * Predicate Contract
 *
 * Predicates are classes that represent bool-valued functions, which can be used
 * to test values.
 *
 * @template V of mixed
 */
interface Predicate
{
    /**
     * Test the provided value.
     *
     * This method will test the provided value in a manner specific to the
     * implementation, returning true if it passes, and false otherwise.
     *
     * @param V $value
     *
     * @return bool
     */
    public function test(mixed $value): bool;
}