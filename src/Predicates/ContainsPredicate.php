<?php
declare(strict_types=1);

namespace Smpl\Collections\Predicates;

use Smpl\Collections\Contracts\Collection;

/**
 * Contains Predicate
 *
 * A predicate that checks if a provided collection contains the provided value.
 *
 * @template V of mixed
 * @template-extends \Smpl\Collections\Predicates\BasePredicate<V>
 */
final class ContainsPredicate extends BasePredicate
{
    /**
     * @var \Smpl\Collections\Contracts\Collection<V>
     */
    private Collection $collection;

    /**
     * @param \Smpl\Collections\Contracts\Collection<V> $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param V $value
     *
     * @return bool
     */
    public function test(mixed $value): bool
    {
        return $this->collection->contains($value);
    }
}