<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

/**
 * New Collection Of Elements Concern
 *
 * This concerns exists to provide a default implementation of
 * {@see \Smpl\Collections\Contracts\Collection::of()}, the static variadic
 * constructor for collections.
 *
 * @template I of mixed
 * @template E of mixed
 * @requires \Smpl\Collections\Contracts\Collection<I, E>
 * @psalm-immutable
 */
trait NewCollectionOfElements
{
    /**
     * Create a new instance of this collection for the provided elements.
     *
     * This method is a variadic static constructor for the collection,
     * creating a new instance for all the provided elements.
     *
     * @template NE of mixed
     *
     * @param NE ...$elements
     *
     * @return static
     */
    public static function of(mixed ...$elements): static
    {
        /** @psalm-suppress UnsafeInstantiation */
        return new static($elements);
    }
}