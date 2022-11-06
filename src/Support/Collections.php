<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

use Smpl\Collections\Collection;
use Smpl\Collections\Contracts;
use Smpl\Collections\Helpers\IterableHelper;
use Smpl\Collections\ImmutableCollection;
use Smpl\Collections\ImmutableSet;
use Smpl\Collections\Sequence;
use Smpl\Collections\Set;
use Smpl\Collections\SortedCollection;
use Smpl\Collections\SortedSet;
use Smpl\Utils\Contracts\Comparator;

/**
 *
 */
final class Collections
{
    /**
     * @template       E of mixed
     * @template       R of mixed
     *
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $collection
     * @param iterable<array-key, R>                               $elements
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, R>
     */
    public static function from(Contracts\Collection $collection, iterable $elements): Contracts\Collection
    {
        return $collection->copy($elements);
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E> $elements
     *
     * @return \Smpl\Collections\Collection<E>
     */
    public static function collection(iterable $elements = []): Collection
    {
        return new Collection($elements);
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E> $elements
     *
     * @return \Smpl\Collections\ImmutableCollection<E>
     */
    public static function immutableCollection(iterable $elements = []): ImmutableCollection
    {
        return new ImmutableCollection(array_values(IterableHelper::iterableToArray($elements)));
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E>                   $elements
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return \Smpl\Collections\SortedCollection<E>
     */
    public static function sortedCollection(iterable $elements = [], ?Comparator $comparator = null): SortedCollection
    {
        return new SortedCollection($elements, $comparator);
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E> $elements
     *
     * @return \Smpl\Collections\Sequence<E>
     */
    public static function sequence(iterable $elements = []): Sequence
    {
        return new Sequence($elements);
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E> $elements
     *
     * @return \Smpl\Collections\Set<E>
     */
    public static function set(iterable $elements): Set
    {
        return new Set($elements);
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E> $elements
     *
     * @return \Smpl\Collections\ImmutableSet<E>
     */
    public static function immutableSet(iterable $elements): ImmutableSet
    {
        return new ImmutableSet($elements);
    }

    /**
     * @template E of mixed
     *
     * @param iterable<array-key, E>                   $elements
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return \Smpl\Collections\SortedSet<E>
     */
    public static function sortedSet(iterable $elements, ?Comparator $comparator = null): SortedSet
    {
        return new SortedSet($elements, $comparator);
    }
}