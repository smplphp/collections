<?php
declare(strict_types=1);

namespace Smpl\Collections\Comparators;

use Smpl\Collections\Contracts\Comparator;

/**
 * Base Comparator
 *
 * A base comparator to provide the default __invoke implementation.
 *
 * @template V of mixed
 * @template-implements \Smpl\Collections\Contracts\Comparator<V>
 */
abstract class BaseComparator implements Comparator
{
    /**
     * @param V $a
     * @param V $b
     *
     * @return int
     */
    public function __invoke(mixed $a, mixed $b): int
    {
        return $this->compare($a, $b);
    }
}