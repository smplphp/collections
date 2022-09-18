<?php
declare(strict_types=1);

namespace Smpl\Collections\Comparators;

/**
 * Callable Comparator
 *
 * A comparator that wraps a callable.
 *
 * @template V of mixed
 * @template-extends \Smpl\Collections\Comparators\BaseComparator<V>
 */
final class CallableComparator extends BaseComparator
{
    /**
     * @var callable(V, V):int
     */
    private $callable;

    /**
     * @param callable(V, V):int $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param V $a
     * @param V $b
     *
     * @return int
     */
    public function compare(mixed $a, mixed $b): int
    {
        $callable = $this->callable;

        return $callable($a, $b);
    }
}