<?php
declare(strict_types=1);

namespace Smpl\Collections\Comparators;

/**
 * Default Comparator
 *
 * This comparator serves as a default implementation making use of PHPs
 * spaceship operator.
 *
 * @template V of mixed
 * @extends \Smpl\Collections\Comparators\BaseComparator<V>
 */
final class DefaultComparator extends BaseComparator
{
    /**
     * @param V $a
     * @param V $b
     *
     * @return int<-1, 1>
     *
     * @psalm-pure
     * @phpstan-pure
     */
    public function compare(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }
}