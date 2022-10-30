<?php

namespace Smpl\Collections\Contracts;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Queue<E>
 */
interface PriorityQueue extends Queue
{
    /**
     * Prioritise the queue in ascending order.
     */
    public const ASC_ORDER = 1;

    /**
     * Prioritise the queue in descending order.
     */
    public const DESC_ORDER = 2;

    /**
     * Prioritise the queue with null values first.
     */
    public const NULL_VALUE_FIRST = 4;

    /**
     * Prioritise the queue with null values last.
     */
    public const NULL_VALUE_LAST = 8;

    /**
     * Do not allow null values.
     */
    public const NO_NULL = 16;

    /**
     * Put elements with no priority first.
     */
    public const NO_PRIORITY_FIRST = 32;

    /**
     * Put elements with no priority last.
     */
    public const NO_PRIORITY_LAST = 64;

    /**
     * Ensure that this collection contains the provided element.
     *
     * This method will ensure that the collection contains the provided element,
     * returning true if the collection was modified. In the case of the
     * implementor not allowing duplicates, this method will return false.
     *
     * If the element provided cannot be added to the collection for a reason
     * other than it being duplicate, such as it being null, the implementor
     * must throw a {@see \Smpl\Collections\Exceptions\InvalidArgumentException}
     * exception.
     *
     * If $priority is provided, $element will be added with the provided priority.
     * If $element is already present in the collection, and $priority isn't
     * false, the priority of the existing element should be updated. In all
     * other cases, false should be treated the same as null.
     *
     * @param E              $element
     * @param int|false|null $priority
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public function add(mixed $element, int|false|null $priority = false): bool;

    /**
     * Ensure that this collection contains all the provided elements.
     *
     * This method will function exactly like
     * {@see \Smpl\Collections\Contracts\MutableCollection::add()} except that it
     * deals with multiple elements, rather than just one.
     *
     * Because of this, it is possible for this method to return true, even if
     * only one of the provided elements are actually added to this collection.
     *
     * This method must also throw a {@see \Smpl\Collections\Exceptions\InvalidArgumentException}
     * if any of the provided elements cannot be added to the collection for
     * reasons other than being a duplicate.
     *
     * If $priority is provided, $element will be added with the provided priority.
     * If $element is already present in the collection, the collection does
     * not allow duplicates, and $priority isn't false, the priority of the
     * existing element should be updated. In all other cases, false should be
     * treated the same as null.
     *
     * @param iterable<E>    $elements
     * @param int|false|null $priority
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public function addAll(iterable $elements, int|false|null $priority = null): bool;

    /**
     * Get the priority queue flags.
     *
     * This method will return the integer mask for this collections flags.
     *
     * @return int
     */
    public function flags(): int;

    /**
     * Get the priority for the provided element.
     *
     * This method will return the first priority for the provided element. If
     * a {@see \Smpl\Utils\Contracts\Comparator} is present, that will be used to
     * find the element and its priority.
     *
     * If the element is found, an int is returned if it has a priority, otherwise
     * null will be returned. If no matching element is found, false is returned.
     *
     * @param E $element
     *
     * @return int|null|false
     */
    public function priority(mixed $element): int|null|false;
}