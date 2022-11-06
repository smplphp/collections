<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Utils\Contracts\Predicate;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class FilterOperation extends BaseOperation
{
    /**
     * @var \Smpl\Utils\Contracts\Predicate<E>
     */
    private Predicate $filter;

    /**
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     */
    public function __construct(Predicate $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        $value->removeIf($this->filter->negate());

        return $value;
    }
}