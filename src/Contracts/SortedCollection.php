<?php

namespace Smpl\Collections\Contracts;

use Smpl\Utils\Contracts\ComparesValues;

/**
 * Sorted Collection
 *
 * This contract is an extension of {@see \Smpl\Collections\Contracts\Collection}
 * for collections that should impose a sorting order using a
 * {@see \Smpl\Utils\Contracts\Comparator}.
 *
 * As well as allowing collections to impose a sorting order, this contract
 * provides a number of methods that provide navigable functionality, allowing
 * for the retrieval of elements based on their relative value, rather
 * than their exact value.
 *
 * @template I of array-key
 * @template E of mixed
 * @extends \Smpl\Utils\Contracts\ComparesValues<E>
 */
interface SortedCollection extends ComparesValues
{
    /**
     * Get the least element greater than or equal to the provided element.
     *
     * This method will get all elements from this collection that are greater
     * than or equal to $element, and return the least of them, or null.
     *
     * @param E $element
     *
     * @return E|null
     */
    public function ceiling(mixed $element): mixed;

    /**
     * Get the greatest element less than or equal to the provided element.
     *
     * This method will get all elements from this collection that are greater
     * than or equal to $element, and return the greatest of them, or null.
     *
     * @param E $element
     *
     * @return E|null
     */
    public function floor(mixed $element): mixed;

    /**
     * Get all elements that are less than the provided element.
     *
     * This method will return a subset of the elements contained within this
     * collection, that are strictly less than $toElement. If $inclusive is
     * true, it will also contain elements that are equal to $toElement.
     *
     * @param E    $toElement
     * @param bool $inclusive
     *
     * @return static
     */
    public function headset(mixed $toElement, bool $inclusive = false): static;

    /**
     * Get the least element greater than the provided element.
     *
     * This method will get all elements from this collection that are greater
     * than $element, and return the least of them, or null.
     *
     * This method functions just like
     * {@see \Smpl\Collections\Contracts\SortedCollection::ceiling()}, except
     * that it doesn't include elements that are considered equal to $element.
     *
     * @param E $element
     *
     * @return E|null
     */
    public function higher(mixed $element): mixed;

    /**
     * Get the greatest element less than the provided element.
     *
     * This method will get all elements from this collection that are less
     * than $element, and return the greatest of them, or null.
     *
     * This method functions just like
     * {@see \Smpl\Collections\Contracts\SortedCollection::floor()}, except
     * that it doesn't include elements that are considered equal to $element.
     *
     * @param E $element
     *
     * @return E|null
     */
    public function lower(mixed $element): mixed;

    /**
     * Get all elements that are greater than one element, but less than the other.
     *
     * This method will return a subset of this collection containing only elements
     * that are greater than $fromElement, but less than $toElement.
     *
     * If $fromInclusive is set to true, the returned collection will also include
     * elements that are considered equal to $fromElement.
     *
     * If $toInclusive is set to true, the returned collection will also include
     * elements that are considered equal to $toElement.
     *
     * @param E    $fromElement
     * @param E    $toElement
     * @param bool $fromInclusive
     * @param bool $toInclusive
     *
     * @return static
     */
    public function subset(mixed $fromElement, mixed $toElement, bool $fromInclusive = false, bool $toInclusive = false): static;

    /**
     * Get all elements that are greater than the provided element.
     *
     * This method will return a subset of the elements contained within this
     * collection, that are strictly greater than $fromElement. If $inclusive is
     * true, it will also contain elements that are equal to $fromElement.
     *
     * @param E    $fromElement
     * @param bool $inclusive
     *
     * @return static
     */
    public function tailset(mixed $fromElement, bool $inclusive = false): static;
}