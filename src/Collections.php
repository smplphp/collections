<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * Collections Helper Class
 *
 * This class provides an easy way to create instances of collections.
 */
final class Collections
{
    /**
     * Create a mutable collection for the provided elements.
     *
     * @template E of mixed
     *
     * @param iterable<E> $elements
     *
     * @return \Smpl\Collections\Contracts\CollectionMutable<E>
     */
    public static function collect(iterable $elements): Contracts\CollectionMutable
    {
        return new Collection($elements);
    }

    /**
     * Create an immutable collection for the provided elements.
     *
     * @template E of mixed
     *
     * @param iterable<E> $elements
     *
     * @return \Smpl\Collections\Contracts\Collection<E>
     */
    public static function collectImmutable(iterable $elements): Contracts\Collection
    {
        return new ImmutableCollection($elements);
    }
}