<?php

namespace Smpl\Collections\Contracts;

use Iterator;

/**
 * Deque Contract
 *
 * This contract represents a deque, which stands for double-ended-queue and
 * is pronounced deck. A Deque functions as both
 * {@see \Smpl\Collections\Contracts\Queue} and {@see \Smpl\Collections\Contracts\Stack}.
 *
 * While implementations function as both Queue and Stack, they will have to
 * default to one or the other, consistently, for the purpose of the following
 * methods.
 *
 *   - {@see \Smpl\Collections\Contracts\Deque::peek()}
 *   - {@see \Smpl\Collections\Contracts\Deque::poll()}
 *
 * These methods are provided by this contract in an attempt
 * to provide some consistency. Each of these methods, either through the
 * Queue, Stack or this contract will have a variation suffixed with "last"
 * or "first" for more controlled handling.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Queue<E>
 * @extends \Smpl\Collections\Contracts\Stack<E>
 */
interface Deque extends Queue, Stack
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
     * @param E $element
     *
     * @return bool
     *
     * @see \Smpl\Collections\Contracts\Collection::add()
     */
    public function addFirst(mixed $element): bool;

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
     * @param E $element
     *
     * @return bool
     *
     * @see \Smpl\Collections\Contracts\Collection::add()
     */
    public function addLast(mixed $element): bool;

    /**
     * Get this deque as a queue.
     *
     * This method returns this collection as a queue.
     *
     * Because of the ambiguity of this type of collection, this method should
     * return a new, independent instance of the {@see \Smpl\Collections\Contracts\Queue}
     * contract.
     *
     * @return \Smpl\Collections\Contracts\Queue<E>
     */
    public function asQueue(): Queue;

    /**
     * Get this deque as a stack.
     *
     * This method returns this collection as a stack.
     *
     * Because of the ambiguity of this type of collection, this method should
     * return a new, independent instance of the {@see \Smpl\Collections\Contracts\Stack}
     * contract.
     *
     * @return \Smpl\Collections\Contracts\Stack<E>
     */
    public function asStack(): Stack;

    /**
     * Gets but does not remove the first or last element in the queue, or null.
     *
     * This method will return the first or last element in the queue, if there
     * is one, without modifying the queue itself.
     *
     * Whether the element returned is the first, or last, will be defined by
     * the implementation, and whether it's acting as a Queue or a Stack.
     *
     * @return E
     *
     * @see \Smpl\Collections\Contracts\Queue::peekFirst()
     * @see \Smpl\Collections\Contracts\Stack::peekLast()
     */
    public function peek(): mixed;

    /**
     * Removes the first or last element of the queue, and returns it.
     *
     * This method will return the first or last element in the queue, if there
     * is one, removing it in the process.
     *
     * Whether the element returned is the first, or last, will be defined by
     * the implementation, and whether it's acting as a Queue or a Stack.
     *
     * @return E
     *
     * @see \Smpl\Collections\Contracts\Queue::peekFirst()
     * @see \Smpl\Collections\Contracts\Stack::peekLast()
     */
    public function poll(): mixed;

    /**
     * Get an ascending iterator.
     *
     * This method will return an iterator that iterates over the elements
     * in this collection in ascending order, starting with the first element.
     *
     * @return \Iterator<int, E>
     */
    public function ascendingIterator(): Iterator;

    /**
     * Get a descending iterator.
     *
     * This method will return an iterator that iterates over the elements
     * in this collection in descending order, starting with the last element.
     *
     * @return \Iterator<int, E>
     */
    public function descendingIterator(): Iterator;
}