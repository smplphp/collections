<?php
declare(strict_types=1);

namespace Smpl\Collections\Comparators;

use InvalidArgumentException;
use Smpl\Collections\Contracts\Comparable;

/**
 * Comparable Comparator
 *
 * A comparator that compares instances of {@see \Smpl\Collections\Contracts\Comparable}.
 *
 * @template-extends \Smpl\Collections\Comparators\BaseComparator<\Smpl\Collections\Contracts\Comparable>
 */
class ComparableComparator extends BaseComparator
{
    /**
     * @param \Smpl\Collections\Contracts\Comparable|mixed $a
     * @param \Smpl\Collections\Contracts\Comparable|mixed $b
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function compare(mixed $a, mixed $b): int
    {
        if ((! ($a instanceof Comparable)) || (! ($b instanceof Comparable))) {
            throw new InvalidArgumentException(
                'Values for comparison using the ' . self::class . ' must implement ' . Comparable::class
            );
        }

        return $a->compareTo($b);
    }
}