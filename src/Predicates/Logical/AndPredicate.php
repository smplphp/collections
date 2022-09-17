<?php
declare(strict_types=1);

namespace Smpl\Collections\Predicates\Logical;

use Smpl\Collections\Contracts\Predicate;
use Smpl\Collections\Exceptions\PredicateException;
use Smpl\Collections\Predicates\BasePredicate;

/**
 * And Predicate
 *
 * A predicate that uses a logical AND to test a single value against multiple
 * predicates.
 *
 * @template V of mixed
 * @template-extends \Smpl\Collections\Predicates\BasePredicate<V>
 */
final class AndPredicate extends BasePredicate
{
    /**
     * @var \Smpl\Collections\Contracts\Predicate<V>[]
     */
    private array $predicates;

    /**
     * @throws \Smpl\Collections\Exceptions\NotEnoughPredicatesException
     */
    public function __construct(Predicate ...$predicates)
    {
        if (count($predicates) < 2) {
            throw PredicateException::notEnough(count($predicates), 2, self::class);
        }

        $this->predicates = $predicates;
    }

    /**
     * @param V $value
     *
     * @return bool
     */
    public function test(mixed $value): bool
    {
        foreach ($this->predicates as $predicate) {
            if (! $predicate($value)) {
                return false;
            }
        }

        return true;
    }
}