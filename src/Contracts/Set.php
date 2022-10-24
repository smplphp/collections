<?php

namespace Smpl\Collections\Contracts;

/**
 * Set Contract
 *
 * This contract represents a collection that does not differ hugely from
 * {@see \Smpl\Collections\Contracts\Collection}, except that it by definition,
 * does not allow duplicate elements.
 *
 * Duplicity is decided by using a {@see \Smpl\Utils\Contracts\Comparator},
 * therefore implementations should ideally, take steps to ensure that one is
 * provided, either by default, or at runtime.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\Contracts\Collection<int, E>
 */
interface Set extends Collection
{

}