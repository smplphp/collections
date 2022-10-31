<?php

namespace Smpl\Collections\Contracts;

/**
 * Priority Stack Contract
 *
 * This contract represents a specific variant of {@see \Smpl\Collections\Contracts\Stack},
 * that allows you to add elements with a numeric priority.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\PrioritisedCollection<E>
 * @extends \Smpl\Collections\Contracts\Stack<E>
 */
interface PriorityStack extends PrioritisedCollection, Stack
{

}