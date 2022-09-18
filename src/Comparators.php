<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Comparators\CallableComparator;
use Smpl\Collections\Comparators\ComparableComparator;
use Smpl\Collections\Comparators\DefaultComparator;

/**
 * Comparators Helper Class
 *
 * This class provides an easy way to create instances of comparators.
 */
final class Comparators
{
    /**
     * Create a default comparator.
     *
     * @return \Smpl\Collections\Comparators\DefaultComparator
     */
    public static function default(): DefaultComparator
    {
        return new DefaultComparator();
    }

    /**
     * Create a comparator for instances of {@see \Smpl\Collections\Contracts\Comparable}.
     *
     * @return \Smpl\Collections\Comparators\ComparableComparator
     */
    public static function comparable(): ComparableComparator
    {
        return new ComparableComparator();
    }

    /**
     * Create a comparator for a callable.
     *
     * @template V of mixed
     *
     * @param callable(V, V):int $callable
     *
     * @return \Smpl\Collections\Comparators\CallableComparator<V>
     */
    public static function ofCallable(callable $callable): CallableComparator
    {
        return new CallableComparator($callable);
    }

    /**
     * Ensure provided comparators are instances of {@see \Smpl\Collections\Contracts\Comparator}.
     *
     * @template V of mixed
     *
     * @param array<\Smpl\Collections\Contracts\Comparator<V>|callable(V, V):int|null> $comparators
     *
     * @return list<\Smpl\Collections\Contracts\Comparator<V>>
     */
    public static function ensureInstances(array $comparators): array
    {
        $instances = [];

        foreach ($comparators as $comparator) {
            $instances[] = self::ensureInstance($comparator);
        }

        return $instances;
    }

    /**
     * Ensure provided comparator is an instances of {@see \Smpl\Collections\Contracts\Comparator}.
     *
     * @template       V of mixed
     *
     * @param \Smpl\Collections\Contracts\Comparator<V>|callable(V, V):int|null $comparator
     *
     * @return \Smpl\Collections\Contracts\Comparator<V>
     *
     * @psalm-suppress InvalidReturnStatement
     */
    public static function ensureInstance(Contracts\Comparator|callable|null $comparator): Contracts\Comparator
    {
        if ($comparator === null) {
            return self::default();
        }

        if ($comparator instanceof Contracts\Comparator) {
            return $comparator;
        }

        return self::ofCallable($comparator);
    }
}