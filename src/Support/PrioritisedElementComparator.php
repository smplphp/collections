<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

use Smpl\Utils\Comparators\BaseComparator;
use Smpl\Utils\Contracts\Comparator;
use function Smpl\Utils\get_sign;

/**
 * Prioritised Element Comparator
 *
 * This comparator exists so that the {@see \Smpl\Collections\PriorityQueue}
 * implementation can make use of a comparator. It wraps another
 * {@see \Smpl\Utils\Contracts\Comparator} instance, and is responsible for
 * retrieving the element of a {@see \Smpl\Collections\Support\PrioritisedElement},
 * so that it can be compared in the wrapped comparator.
 *
 * @template E of mixed
 * @extends \Smpl\Utils\Comparators\BaseComparator<\Smpl\Collections\Support\PrioritisedElement<E>>
 * @internal
 */
final class PrioritisedElementComparator extends BaseComparator
{
    /**
     * @var \Smpl\Utils\Contracts\Comparator<E>
     */
    private Comparator $comparator;

    /**
     * @param \Smpl\Utils\Contracts\Comparator<E> $comparator
     */
    public function __construct(Comparator $comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * @param \Smpl\Collections\Support\PrioritisedElement<E>|E $a
     * @param \Smpl\Collections\Support\PrioritisedElement<E>|E $b
     *
     * @return int<-1,1>
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @psalm-suppress ImpurePropertyFetch
     * @psalm-suppress ImpureVariable
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgument
     *
     * @infection-ignore-all
     */
    public function compare(mixed $a, mixed $b): int
    {
        $a = $a instanceof PrioritisedElement ? $a->getElement() : $a;
        $b = $b instanceof PrioritisedElement ? $b->getElement() : $b;

        return get_sign($this->comparator->compare($a, $b));
    }

    /**
     * Set the wrapped comparator.
     *
     * This method sets the base comparator that this wraps.
     *
     * @param \Smpl\Utils\Contracts\Comparator $comparator
     */
    public function setComparator(Comparator $comparator): void
    {
        $this->comparator = $comparator;
    }
}