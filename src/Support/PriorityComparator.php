<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

use Smpl\Collections\Contracts\PrioritisedCollection;
use Smpl\Collections\Contracts\PriorityQueue;
use Smpl\Utils\Comparators\BaseComparator;
use Smpl\Utils\Helpers\ComparisonHelper;
use function Smpl\Utils\get_sign;

/**
 * Priority Comparator
 *
 * This comparator is used by {@see \Smpl\Collections\PriorityQueue} to order
 * the elements based on their priority.
 *
 * @template E of mixed
 * @extends \Smpl\Utils\Comparators\BaseComparator<\Smpl\Collections\Support\PrioritisedElement<E>>
 * @internal
 */
class PriorityComparator extends BaseComparator
{
    /**
     * @var int
     */
    private int $flags;

    /**
     * @param int $flags
     */
    public function __construct(int $flags)
    {
        $this->flags = $flags;
    }

    /**
     * @param \Smpl\Collections\Support\PrioritisedElement<E> $a
     * @param \Smpl\Collections\Support\PrioritisedElement<E> $b
     *
     * @return int<-1, 1>
     *
     * @psalm-pure
     * @phpstan-pure
     *
     * @psalm-suppress ImpureVariable
     * @psalm-suppress ImpureMethodCall
     *
     * @codeCoverageIgnore
     */
    public function compare(mixed $a, mixed $b): int
    {
        if ($this->nullShouldBeFirst()) {
            if ($a->isNull() && ! $b->isNull()) {
                return ComparisonHelper::LESS_THAN;
            }

            if ($b->isNull() && ! $a->isNull()) {
                return ComparisonHelper::MORE_THAN;
            }
        } else if ($this->nullShouldBeLast()) {
            if ($a->isNull() && ! $b->isNull()) {
                return ComparisonHelper::MORE_THAN;
            }

            if ($b->isNull() && ! $a->isNull()) {
                return ComparisonHelper::LESS_THAN;
            }
        }

        if ($this->noPriorityFirst()) {
            if (! $a->hasPriority() && $b->hasPriority()) {
                return ComparisonHelper::LESS_THAN;
            }

            if (! $b->hasPriority() && $a->hasPriority()) {
                return ComparisonHelper::MORE_THAN;
            }
        } else if ($this->noPriorityLast()) {
            if (! $a->hasPriority() && $b->hasPriority()) {
                return ComparisonHelper::MORE_THAN;
            }

            if (! $b->hasPriority() && $a->hasPriority()) {
                return ComparisonHelper::LESS_THAN;
            }
        }

        $sign = get_sign($a->getPriority() <=> $b->getPriority());

        if ($this->isDescendingOrder()) {
            return ComparisonHelper::flip($sign);
        }

        return $sign;
    }

    /**
     * Check if null elements should be first
     *
     * @return bool
     */
    private function nullShouldBeFirst(): bool
    {
        return ($this->flags & PrioritisedCollection::NULL_VALUE_FIRST) === PrioritisedCollection::NULL_VALUE_FIRST;
    }

    /**
     * Check if null elements should be last.
     *
     * @return bool
     */
    private function nullShouldBeLast(): bool
    {
        return ($this->flags & PrioritisedCollection::NULL_VALUE_LAST) === PrioritisedCollection::NULL_VALUE_LAST;
    }

    /**
     * Check if elements should be in descending order.
     *
     * @return bool
     */
    private function isDescendingOrder(): bool
    {
        return ($this->flags & PrioritisedCollection::DESC_ORDER) === PrioritisedCollection::DESC_ORDER;
    }

    /**
     * Check if elements without a priority should be first.
     *
     * @return bool
     */
    private function noPriorityFirst(): bool
    {
        return ($this->flags & PrioritisedCollection::NO_PRIORITY_FIRST) === PrioritisedCollection::NO_PRIORITY_FIRST;
    }

    /**
     * Check if elements without a priority should be last.
     *
     * @return bool
     */
    private function noPriorityLast(): bool
    {
        return ($this->flags & PrioritisedCollection::NO_PRIORITY_LAST) === PrioritisedCollection::NO_PRIORITY_LAST;
    }
}