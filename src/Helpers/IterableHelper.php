<?php
declare(strict_types=1);

namespace Smpl\Collections\Helpers;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Enumerable;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Helpers\ComparisonHelper;
use Traversable;
use function Smpl\Utils\does_sign_match;

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
     * @param array<E>                                 $iterable
     * @param E                                        $element
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return bool
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses     \in_array()
     * @uses     \Smpl\Utils\Helpers\ComparisonHelper::signum()
     * @uses     \Smpl\Utils\Helpers\ComparisonHelper::EQUAL_TO
     */
    public static function contains(array $iterable, mixed $element, ?Comparator $comparator = null): bool
    {
        if ($comparator !== null) {
            foreach ($iterable as $existingElement) {
                if (does_sign_match($comparator->compare($existingElement, $element), ComparisonHelper::EQUAL_TO)) {
                    return true;
                }
            }

            return false;
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
     * @param array<E>                                 $iterable
     * @param iterable<E>                              $elements
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
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
     * {@see \Smpl\Utils\Comparators\IdenticalityComparator} to compare each
     * element in the iterable against the provided element, returning the number of matches.
     *
     * @template E of mixed
     *
     * @param array<E>                                 $iterable
     * @param E                                        $element
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return int<0, max>
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses     \Smpl\Utils\Comparators\IdenticalityComparator
     * @uses     \Smpl\Utils\Contracts\Comparator::compare()
     * @uses     \Smpl\Utils\Helpers\ComparisonHelper::EQUAL_TO
     */
    public static function countOf(array $iterable, mixed $element, ?Comparator $comparator = null): int
    {
        $comparator ??= new IdenticalityComparator();
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
     * @psalm-pure
     * @phpstan-pure
     *
     * @uses           \Smpl\Collections\Contracts\Collection::toArray()
     * @uses           \Traversable::__clone
     *
     * @infection-ignore-all
     */
    public static function getImpureSafeIterable(iterable $elements): iterable
    {
        if ($elements instanceof Collection) {
            /** @psalm-suppress ImpureMethodCall */
            return $elements->toArray();
        }

        if ($elements instanceof Traversable) {
            return clone $elements;
        }

        /** @psalm-suppress RedundantCastGivenDocblockType */
        return (array)$elements;
    }

    /**
     * Turn an iterable into an array.
     *
     * This method will take an iterable and return an array. If the provided
     * iterable is a collection, the {@see \Smpl\Collections\Contracts\Collection::toArray()}
     * method will be called, otherwise it will use {@see \iterator_to_array()}.
     *
     * @template       I of array-key
     * @template       E of mixed
     *
     * @param iterable<I, E> $keys
     *
     * @return array<I, E>
     */
    public static function iterableToArray(iterable $keys): array
    {
        /** @infection-ignore-all  */
        if ($keys instanceof Enumerable) {
            return $keys->toArray();
        }

        if ($keys instanceof Traversable) {
            return iterator_to_array($keys);
        }

        return $keys;
    }
}