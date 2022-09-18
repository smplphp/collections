<?php
declare(strict_types=1);

namespace Smpl\Collections\Predicates;

use Smpl\Collections\Contracts\Collection;

/**
 * Callable Predicate
 *
 * A predicate that wraps a callable.
 *
 * @template V of mixed
 * @template-extends \Smpl\Collections\Predicates\BasePredicate<V>
 */
final class CallablePredicate extends BasePredicate
{
    /**
     * @var callable(V):bool
     */
    private $callable;

    /**
     * @param callable(V):bool $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param V $value
     *
     * @return bool
     */
    public function test(mixed $value): bool
    {
        $callable = $this->callable;

        return $callable($value);
    }
}