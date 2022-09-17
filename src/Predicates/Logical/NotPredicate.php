<?php
declare(strict_types=1);

namespace Smpl\Collections\Predicates\Logical;

use Smpl\Collections\Contracts\Predicate;
use Smpl\Collections\Predicates\BasePredicate;

/**
 * Not Predicate
 *
 * A predicate that uses a logical negation of another predicate.
 *
 * @template V of mixed
 * @template-extends \Smpl\Collections\Predicates\BasePredicate<V>
 */
final class NotPredicate extends BasePredicate
{
    /**
     * @var \Smpl\Collections\Contracts\Predicate<V>
     */
    private Predicate $predicate;

    /**
     * @param \Smpl\Collections\Contracts\Predicate<V> $predicate
     */
    public function __construct(Predicate $predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * @param V $value
     *
     * @return bool
     */
    public function test(mixed $value): bool
    {
        return ! $this->predicate->test($value);
    }
}