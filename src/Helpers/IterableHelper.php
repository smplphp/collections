<?php
declare(strict_types=1);

namespace Smpl\Collections\Helpers;

use Smpl\Collections\Comparators\IdenticalComparator;
use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Comparator;

/**
 * Iterable Helper
 *
 * A helper class for dealing with iterables, whether they're base 'iterable'
 * types or collections.
 *
 * A lot of the methods in here are replicas of methods in the various
 * collection contracts, but abstracted out to avoid repeating the same code
 * over and over again.
 */
final class IterableHelper
{
    /**
     * Check if the provided element is contained within the provided iterable.
     *
     * This method should return true if the provided element exists within
     * the iterable, identified by using the provided comparator, or
     * {@see \in_array()}.
     *
     * @template E of mixed
     *
     * @param array<E>                                       $iterable
     * @param E                                              $element
     * @param \Smpl\Collections\Contracts\Comparator<E>|null $comparator
     *
     * @return bool
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses     \in_array()
     * @uses     \Smpl\Collections\Helpers\ComparisonHelper::signum()
     * @uses     \Smpl\Collections\Helpers\ComparisonHelper::EQUAL_TO
     */
    public static function contains(array $iterable, mixed $element, ?Comparator $comparator = null): bool
    {
        if ($comparator !== null) {
            foreach ($iterable as $existingElement) {
                if (ComparisonHelper::signum($comparator->compare($existingElement, $element)) === ComparisonHelper::EQUAL_TO) {
                    return true;
                }
            }
        }

        return in_array($element, $iterable, true);
    }

    /**
     * Check if the provided elements are contained within the provided iterable.
     *
     * This method should return true if all the provided elements exist within
     * the iterable, identified by calling
     * {@see \Smpl\Collections\Helpers\IterableHelper::contains()}.
     *
     * Should any of the provided elements not be found in the iterable,
     * this method should return false.
     *
     * While the best of efforts have been made to try and ensure the immutable
     * and pure nature of this method, it is still possible that the provided
     * iterable may be modified during iteration.
     *
     * @template E of mixed
     *
     * @param array<E>                                       $iterable
     * @param iterable<E>                                    $elements
     * @param \Smpl\Collections\Contracts\Comparator<E>|null $comparator
     *
     * @return bool
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses     \Smpl\Collections\Helpers\IterableHelper::contains()
     * @uses     \Smpl\Collections\Helpers\IterableHelper::getImpureSafeIterable()
     */
    public static function containsAll(array $iterable, iterable $elements, ?Comparator $comparator = null): bool
    {
        $elements = self::getImpureSafeIterable($elements);

        /** @psalm-suppress ImpureMethodCall */
        foreach ($elements as $element) {
            if (! self::contains($iterable, $element, $comparator)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the number of times the provided element appears in the provided iterable.
     *
     * This method will use the provided comparator, or
     * {@see \Smpl\Collections\Comparators\IdenticalComparator} to compare each
     * element in the iterable against the provided element, returning the number of matches.
     *
     * @template E of mixed
     *
     * @param array<E>                                       $iterable
     * @param E                                              $element
     * @param \Smpl\Collections\Contracts\Comparator<E>|null $comparator
     *
     * @return int<0, max>
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses     \Smpl\Collections\Comparators\IdenticalComparator
     * @uses     \Smpl\Collections\Contracts\Comparator::compare()
     * @uses     \Smpl\Collections\Helpers\ComparisonHelper::EQUAL_TO
     */
    public static function countOf(array $iterable, mixed $element, ?Comparator $comparator = null): int
    {
        $comparator ??= new IdenticalComparator();
        $count      = 0;

        foreach ($iterable as $existingElement) {
            if ($comparator->compare($existingElement, $element) === ComparisonHelper::EQUAL_TO) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Turn an iterable into an impure safe iterable.
     *
     * Since iterable types cannot be guaranteed to be immutable this method
     * will do its best to create a copy of the iterable, optionally casting to
     * a type that is more likely to be immutable.
     *
     * While the best of efforts have been made to try and ensure the immutable
     * and pure nature of the iterable, it is still possible modification may
     * occur during iteration.
     *
     * @template       E of mixed
     *
     * @param iterable<E> $elements
     *
     * @return iterable<E>
     *
     * @psalm-suppress InvalidReturnType
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses \Smpl\Collections\Contracts\Collection::toArray()
     * @uses \Traversable::__clone
     */
    public static function getImpureSafeIterable(iterable $elements): iterable
    {
        if ($elements instanceof Collection) {
            /**
             * @psalm-suppress InvalidReturnStatement
             * @psalm-suppress ImpureMethodCall
             */
            return $elements->toArray();
        }

        if ($elements instanceof \Traversable) {
            return clone $elements;
        }

        /** @psalm-suppress RedundantCastGivenDocblockType */
        return (array)$elements;
    }
}