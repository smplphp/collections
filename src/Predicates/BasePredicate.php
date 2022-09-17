<?php
declare(strict_types=1);

namespace Smpl\Collections\Predicates;

use Smpl\Collections\Contracts\Predicate;
use Smpl\Collections\Predicates;
use Smpl\Collections\Predicates\Logical\AndPredicate;
use Smpl\Collections\Predicates\Logical\NotPredicate;
use Smpl\Collections\Predicates\Logical\OrPredicate;

/**
 * Base Predicate
 *
 * A base predicate to provide the default __invoke implementation.
 *
 * @template V of mixed
 * @template-implements \Smpl\Collections\Contracts\Predicate<V>
 */
abstract class BasePredicate implements Predicate
{
    /**
     * @param V $value
     *
     * @return bool
     */
    public function __invoke(mixed $value): bool
    {
        return $this->test($value);
    }

    /**
     * @return \Smpl\Collections\Predicates\Logical\NotPredicate<V>
     */
    public function negate(): NotPredicate
    {
        return new NotPredicate($this);
    }

    /**
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool $predicate
     *
     * @return \Smpl\Collections\Predicates\Logical\AndPredicate<V>
     *
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function and(callable|Predicate $predicate): AndPredicate
    {
        return Predicates::and($this, $predicate);
    }

    /**
     * @param \Smpl\Collections\Contracts\Predicate<V>|callable(V):bool $predicate
     *
     * @return \Smpl\Collections\Predicates\Logical\OrPredicate<V>
     *
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function or(callable|Predicate $predicate): OrPredicate
    {
        return Predicates::or($this, $predicate);
    }
}