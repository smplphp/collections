<?php

namespace Smpl\Collections\Contracts;

use Smpl\Utils\Contracts\Predicate;

/**
 * Chainable Collection Contract
 *
 * This contract is an extension of {@see \Smpl\Collections\Contracts\MutableCollection},
 * providing chainable alternatives to its methods that modify the collection,
 * returning a boolean.
 *
 * The idea behind the methods contained within this contract are purely for the
 * purpose of convenience, as sometimes you need to worry about the result of an
 * operation, just that it happened.
 *
 * @template I of mixed
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Collection<I, E>
 */
interface ChainableCollection extends Collection
{
    /**
     * Push an element onto this collection.
     *
     * This method will add the provided element to this collection, if it can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\MutableCollection::add()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param E $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::add()
     */
    public function push(mixed $element): static;

    /**
     * Push all the provided elements onto this collection.
     *
     * This method will add all the provided elements to this collection, if they
     * can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\MutableCollection::addAll()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::addAll()
     */
    public function pushAll(iterable $elements): static;

    /**
     * Forget the provided element.
     *
     * This method will remove the provided element from this collection, if it
     * can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\MutableCollection::remove()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param E $element
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::remove()
     */
    public function forget(mixed $element): static;

    /**
     * Forget all the provided elements.
     *
     * This method will remove all the provided elements from this collection, if
     * they can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\MutableCollection::removeAll()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::removeAll()
     */
    public function forgetAll(iterable $elements): static;

    /**
     * Forget all elements that pass the provided filter.
     *
     * This method will remove all elements from this collection that pass
     * the provided filter.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\MutableCollection::removeIf()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::removeIf()
     */
    public function forgetIf(Predicate $filter): static;

    /**
     * Forget all elements not in the provided elements.
     *
     * This method will remove all elements from this collection that are not
     * contained in the provided elements.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\MutableCollection::retainAll()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::retainAll()
     */
    public function keepAll(iterable $elements): static;
}