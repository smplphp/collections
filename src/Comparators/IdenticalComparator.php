<?php
declare(strict_types=1);

namespace Smpl\Collections\Comparators;

use Smpl\Collections\Helpers\ComparisonHelper;

/**
 * Identical Comparator
 *
 * This comparator compares values to see if they're identical. Unlike many
 * other implementations, this will only return for equal to and less than, as
 * the concept of a value being more than identical to another value just doesn't
 * make sense.
 *
 * @template V of mixed
 * @extends \Smpl\Collections\Comparators\BaseComparator<V>
 */
final class IdenticalComparator extends BaseComparator
{
    /**
     * @param V $a
     * @param V $b
     *
     * @return int<-1, 0>
     *
     * @psalm-pure
     * @phpstan-pure
     */
    public function compare(mixed $a, mixed $b): int
    {
        if ($a === $b) {
            return ComparisonHelper::EQUAL_TO;
        }

        return ComparisonHelper::LESS_THAN;
    }
}