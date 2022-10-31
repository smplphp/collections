<?php

namespace Smpl\Collections\Contracts;

/**
 * Priority Deque Contract
 *
 * This is an extension of {@see \Smpl\Collections\Contracts\Deque} that allows
 * you to add elements with a priority.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\PriorityQueue<E>
 * @extends \Smpl\Collections\Contracts\PriorityStack<E>
 * @extends \Smpl\Collections\Contracts\Deque<E>
 */
interface PriorityDeque extends PriorityQueue, PriorityStack, Deque
{
    /**
     * Ensure that the first element in this collection contains the provided element.
     *
     * This method will ensure that the first element in this collection is the
     * provided element, returning true if the collection was modified. In the
     * case of the implementor not allowing duplicates, this method will return
     * false.
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
     * Because elements are ordered based on their priority, this method is no
     * different to {@see \Smpl\Collections\Contracts\PriorityDeque::add()}.
     *
     * @param E              $element
     * @param int|false|null $priority
     *
     * @return bool
     *
     * @see \Smpl\Collections\Contracts\Collection::add()
     */
    public function addFirst(mixed $element, int|false|null $priority = false): bool;

    /**
     * Ensure that the last element in this collection contains the provided element.
     *
     * This method will ensure that the last element in this collection is the
     * provided element, returning true if the collection was modified. In the
     * case of the implementor not allowing duplicates, this method will return
     * false.
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
     * Because elements are ordered based on their priority, this method is no
     * different to {@see \Smpl\Collections\Contracts\PriorityDeque::add()}.
     *
     * @param E              $element
     * @param int|false|null $priority
     *
     * @return bool
     *
     * @see \Smpl\Collections\Contracts\Collection::add()
     */
    public function addLast(mixed $element, int|false|null $priority = false): bool;

    /**
     * Get this deque as a priority queue.
     *
     * This method returns this collection as a priority queue.
     *
     * Because of the ambiguity of this type of collection, this method should
     * return a new, independent instance of the {@see \Smpl\Collections\Contracts\PriorityQueue}
     * contract.
     *
     * @return \Smpl\Collections\Contracts\PriorityQueue<E>
     */
    public function asPriorityQueue(): PriorityQueue;

    /**
     * Get this deque as a priority stack.
     *
     * This method returns this collection as a priority stack.
     *
     * Because of the ambiguity of this type of collection, this method should
     * return a new, independent instance of the {@see \Smpl\Collections\Contracts\PriorityStack}
     * contract.
     *
     * @return \Smpl\Collections\Contracts\PriorityStack<E>
     */
    public function asPriorityStack(): PriorityStack;
}