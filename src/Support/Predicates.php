<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

use Smpl\Collections\Contracts;
use Smpl\Collections\Contracts\Predicate;
use Smpl\Collections\Predicates\CallablePredicate;
use Smpl\Collections\Predicates\ContainsPredicate;
use Smpl\Collections\Predicates\Logical\AndPredicate;
use Smpl\Collections\Predicates\Logical\NotPredicate;
use Smpl\Collections\Predicates\Logical\OrPredicate;

/**
 * Predicate Helper Class
 *
 * This class provides an easy way to create instances of predicates.
 */
final class Predicates
{
    /**
     * Create a predicate that checks a collection for the provided value.
     *
     * @template V of mixed
     *
     * @param \Smpl\Collections\Contracts\Collection<V> $collection
     *
     * @return \Smpl\Collections\Predicates\ContainsPredicate<V>
     *
     * @see      \Smpl\Collections\Contracts\Collection::contains()
     */
    public static function contains(Contracts\Collection $collection): ContainsPredicate
    {
        return new ContainsPredicate($collection);
    }

    /**
     * Create a logical AND predicate for multiple predicates.
     *
     * @template V of mixed
     *
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool ...$predicates
     *
     * @return \Smpl\Collections\Predicates\Logical\AndPredicate<V>
     *
     * @throws \Smpl\Collections\Exceptions\NotEnoughPredicatesException
     */
    public static function and(Predicate|callable ...$predicates): AndPredicate
    {
        return new AndPredicate(...self::ensureInstances($predicates));
    }

    /**
     * Create a logical OR predicate for multiple predicates.
     *
     * @template V of mixed
     *
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool ...$predicates
     *
     * @return \Smpl\Collections\Predicates\Logical\OrPredicate<V>
     *
     * @throws \Smpl\Collections\Exceptions\NotEnoughPredicatesException
     */
    public static function or(Predicate|callable ...$predicates): OrPredicate
    {
        return new OrPredicate(...self::ensureInstances($predicates));
    }

    /**
     * Creates a predicate that negates/inverts the result of another.
     *
     * @template V of mixed
     *
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool $predicate
     *
     * @return \Smpl\Collections\Predicates\Logical\NotPredicate<V>
     */
    public static function not(Predicate|callable $predicate): NotPredicate
    {
        return new NotPredicate(self::ensureInstance($predicate));
    }

    /**
     * Create a predicate for a callable.
     *
     * @template V of mixed
     *
     * @param callable(V):bool $callable
     *
     * @return \Smpl\Collections\Predicates\CallablePredicate<V>
     */
    public static function ofCallable(callable $callable): CallablePredicate
    {
        return new CallablePredicate($callable);
    }

    /**
     * Ensure provided predicates are instances of {@see \Smpl\Collections\Contracts\Predicate}.
     *
     * @template V of mixed
     *
     * @param array<\Smpl\Collections\Contracts\Predicate<V>|callable(V):bool> $predicates
     *
     * @return list<\Smpl\Collections\Contracts\Predicate<V>>
     */
    public static function ensureInstances(array $predicates): array
    {
        $instances = [];

        foreach ($predicates as $predicate) {
            $instances[] = self::ensureInstance($predicate);
        }

        return $instances;
    }

    /**
     * Ensure provided predicate is an instances of {@see \Smpl\Collections\Contracts\Predicate}.
     *
     * @template V of mixed
     *
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool $predicate
     *
     * @return \Smpl\Collections\Contracts\Predicate<V>
     */
    public static function ensureInstance(Predicate|callable $predicate): Predicate
    {
        if ($predicate instanceof Predicate) {
            return $predicate;
        }

        return self::ofCallable($predicate);
    }
}