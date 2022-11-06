<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Operations\ConcatOperation;
use Smpl\Collections\Operations\DifferenceOperation;
use Smpl\Collections\Operations\DistinctOperation;
use Smpl\Collections\Operations\DropOperation;
use Smpl\Collections\Operations\FilterOperation;
use Smpl\Collections\Operations\FlatMapOperation;
use Smpl\Collections\Operations\FlattenOperation;
use Smpl\Collections\Operations\GroupByOperation;
use Smpl\Collections\Operations\IntersectionOperation;
use Smpl\Collections\Operations\JoinOperation;
use Smpl\Collections\Operations\MapOperation;
use Smpl\Collections\Operations\ReduceOperation;
use Smpl\Collections\Operations\RejectOperation;
use Smpl\Collections\Operations\ValidateOperation;
use Smpl\Utils\Contracts\BiFunc;
use Smpl\Utils\Contracts\Func;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template R of mixed
 * @template I of array-key
 * @template E of mixed
 */
final class Pipeline
{
    /**
     * @template NR of mixed
     * @template NI of array-key
     * @template NE of mixed
     *
     * @param \Smpl\Collections\Contracts\Collection<NI, NE> $collection
     *
     * @return \Smpl\Collections\Pipeline<NR, NI, NE>
     */
    public static function for(Contracts\Collection $collection): Pipeline
    {
        return new self($collection);
    }

    /**
     * @var \Smpl\Collections\Contracts\Collection<I, E>
     */
    private Contracts\Collection $collection;

    /**
     * @var \Smpl\Collections\Contracts\Queue<\Smpl\Utils\Contracts\Func>
     */
    private Contracts\Queue $queue;

    /**
     * @param \Smpl\Collections\Contracts\Collection<I, E> $collection
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    private function __construct(Contracts\Collection $collection)
    {
        $this->collection = $collection;
        $this->queue      = new Queue();
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @param \Smpl\Utils\Contracts\Func $operation
     *
     * @return static
     */
    public function then(Func $operation): static
    {
        $this->queue->add($operation);
        return $this;
    }

    /**
     * @param \Smpl\Utils\Contracts\Supplier<\Smpl\Collections\Contracts\Collection<array-key, E>>|iterable<E> $concatWith
     *
     * @return static
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function concat(Supplier|iterable $concatWith): static
    {
        return $this->then(new ConcatOperation($concatWith));
    }

    /**
     * @param \Smpl\Utils\Contracts\Supplier<\Smpl\Collections\Contracts\Collection<array-key, E>>|iterable<E> $diffWith
     *
     * @return static
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function diff(Supplier|iterable $diffWith): static
    {
        return $this->then(new DifferenceOperation($diffWith));
    }

    public function distinct(): static
    {
        return $this->then(new DistinctOperation());
    }

    /**
     * @param int<0, max> $drop
     *
     * @return static
     */
    public function drop(int $drop): static
    {
        return $this->then(new DropOperation($drop));
    }

    public function filter(Predicate $filter): static
    {
        return $this->then(new FilterOperation($filter));
    }

    public function flatMap(Func $mappingFunction): static
    {
        return $this->then(new FlatMapOperation($mappingFunction));
    }

    public function flatten(): static
    {
        return $this->then(new FlattenOperation());
    }

    /**
     * @param \Smpl\Utils\Contracts\BiFunc<E, I, array-key>|\Smpl\Utils\Contracts\Func<E, array-key> $groupingFunction
     *
     * @return static
     */
    public function groupBy(BiFunc|Func $groupingFunction): static
    {
        return $this->then(new GroupByOperation($groupingFunction));
    }

    /**
     * @param \Smpl\Utils\Contracts\Supplier<\Smpl\Collections\Contracts\Collection<array-key, E>>|iterable<E> $intersectWith
     *
     * @return static
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function intersect(Supplier|iterable $intersectWith): static
    {
        return $this->then(new IntersectionOperation($intersectWith));
    }

    /**
     * @param string $glue
     *
     * @return R
     */
    public function join(string $glue = ''): mixed
    {
        $this->then(new JoinOperation($glue));

        return $this->run();
    }

    /**
     * @param \Smpl\Utils\Contracts\Func<E, array-key> $mappingFunction
     *
     * @return static
     */
    public function map(Func $mappingFunction): static
    {
        return $this->then(new MapOperation($mappingFunction));
    }

    /**
     * @param \Smpl\Utils\Contracts\BiFunc<E, R|null, R> $reductionFunction
     * @param R|null                                     $initialValue
     *
     * @return R
     */
    public function reduce(BiFunc $reductionFunction, mixed $initialValue = null): mixed
    {
        $this->then(new ReduceOperation($reductionFunction, $initialValue));

        return $this->run();
    }

    public function reject(Predicate $filter): static
    {
        return $this->then(new RejectOperation($filter));
    }

    /**
     * @template T of \Throwable
     *
     * @param \Smpl\Utils\Contracts\Predicate                                                     $filter
     * @param \Smpl\Utils\Contracts\Func<\Smpl\Collections\Contracts\Collection<array-key, E>, T> $throwable
     *
     * @return static
     */
    public function validate(Predicate $filter, Func $throwable): static
    {
        return $this->then(new ValidateOperation($filter, $throwable));
    }

    /**
     * @return R
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function run(): mixed
    {
        $value = $this->collection;

        foreach ($this->queue as $operation) {
            $value = $operation->apply($value);
        }

        return $value;
    }
}