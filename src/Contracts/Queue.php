<?php

namespace Smpl\Collections\Contracts;

/**
 * Queue Contract
 *
 * This contract represents a queue, a collection designed to hold elements to
 * be processed. It is a basic extension of {@see \Smpl\Collections\Contracts\Collection}.
 *
 * Queues are typically implemented as a FIFO (first-in-first-out) collection,
 * though this will vary between implementations.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Collection<int, E>
 */
interface Queue extends Collection
{
    /**
     * Gets but does not remove the first element in the queue, or null.
     *
     * This method will return the first element in the queue, if there is one,
     * without modifying the queue itself.
     *
     * @return E|null
     */
    public function peek(): mixed;

    /**
     * Removes the first element of the queue, and returns it.
     *
     * This method will return the first element in the queue, if there is one,
     * removing it in the process.
     *
     * @return E|null
     */
    public function poll(): mixed;
}
