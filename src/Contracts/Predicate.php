<?php

namespace Smpl\Collections\Contracts;

/**
 * Predicate Contract
 *
 * Predicates are used to test a given value, returning a boolean. They are
 * typically used for filtering.
 *
 * @template V of mixed
 */
interface Predicate
{
    /**
     * Test a value against the predicate.
     *
     * @param V $value The value to be tested
     *
     * @return bool Returns true if the value passes, false otherwise
     */
    public function test(mixed $value): bool;

    /**
     * Invokable method to let a predicate be treated as a callable.
     *
     * @param V $value
     *
     * @return bool
     *
     * @see \Smpl\Collections\Contracts\Predicate::test()
     */
    public function __invoke(mixed $value): bool;

    /**
     * Get a new predicate that represents the logical negation of this predicate.
     *
     * @return \Smpl\Collections\Contracts\Predicate<V>
     */
    public function negate(): Predicate;

    /**
     * Get a new predicate that uses a logical AND of the predicate and another.
     *
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool $predicate
     *
     * @return \Smpl\Collections\Contracts\Predicate<V>
     */
    public function and(Predicate|callable $predicate): Predicate;

    /**
     * Get a new predicate that uses a logical OR of the predicate and another.
     *
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool $predicate
     *
     * @return \Smpl\Collections\Contracts\Predicate<V>
     */
    public function or(Predicate|callable $predicate): Predicate;
}