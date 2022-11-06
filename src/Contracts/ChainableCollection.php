<?php

namespace Smpl\Collections\Contracts;

use Smpl\Utils\Contracts\Predicate;

/**
 * Chainable Collection Contract
 *
 * This contract is an extension of {@see \Smpl\Collections\Contracts\Collection},
 * providing chainable alternatives to its methods that modify the collection,
 * returning a boolean.
 *
 * The idea behind the methods contained within this contract are purely for the
 * purpose of convenience, as sometimes you need to worry about the result of an
 * operation, just that it happened.
 *
 * @template I of array-key
 * @template E of mixed
 */
interface ChainableCollection
{
    /**
     * Push an element onto this collection.
     *
     * This method will add the provided element to this collection, if it can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\Collection::add()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param E $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\Collection::add()
     */
    public function push(mixed $element): static;

    /**
     * Push all the provided elements onto this collection.
     *
     * This method will add all the provided elements to this collection, if they
     * can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\Collection::addAll()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\Collection::addAll()
     */
    public function pushAll(iterable $elements): static;

    /**
     * Forget the provided element.
     *
     * This method will remove the provided element from this collection, if it
     * can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\Collection::remove()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param E $element
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\Collection::remove()
     */
    public function forget(mixed $element): static;

    /**
     * Forget all the provided elements.
     *
     * This method will remove all the provided elements from this collection, if
     * they can be.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\Collection::removeAll()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\Collection::removeAll()
     */
    public function forgetAll(iterable $elements): static;

    /**
     * Forget all elements that pass the provided filter.
     *
     * This method will remove all elements from this collection that pass
     * the provided filter.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\Collection::removeIf()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\Collection::removeIf()
     */
    public function forgetIf(Predicate $filter): static;

    /**
     * Forget all elements not in the provided elements.
     *
     * This method will remove all elements from this collection that are not
     * contained in the provided elements.
     *
     * This method must function identically to
     * {@see \Smpl\Collections\Contracts\Collection::retainAll()}, except that
     * the collection instance is returned regardless of whether the collection
     * was modified.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\Collection::retainAll()
     */
    public function keepAll(iterable $elements): static;
}