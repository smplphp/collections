<?php
declare(strict_types=1);

namespace Smpl\Collections\Comparators;

/**
 * Default Comparator
 *
 * A default comparator that uses PHPs spaceship operator to compare two values,
 * which uses PHP default type comparison rules.
 *
 * @link     https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.spaceship-op
 * @link     https://www.php.net/manual/en/types.comparisons.php
 *
 * @template V of mixed
 * @template-extends \Smpl\Collections\Comparators\BaseComparator<V>
 */
final class DefaultComparator extends BaseComparator
{
    /**
     * @param V $a
     * @param V $b
     *
     * @return int
     */
    public function compare(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }
}