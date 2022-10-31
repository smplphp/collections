<?php

namespace Smpl\Collections\Contracts;

/**
 * Priority Queue Contract
 *
 * This contract represents a specific variant of {@see \Smpl\Collections\Contracts\Queue},
 * that allows you to add elements with a numeric priority.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\PrioritisedCollection<E>
 * @extends \Smpl\Collections\Contracts\Queue<E>
 */
interface PriorityQueue extends PrioritisedCollection, Queue
{
}