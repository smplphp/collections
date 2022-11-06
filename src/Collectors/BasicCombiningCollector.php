<?php
declare(strict_types=1);

namespace Smpl\Collections\Collectors;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Collector as CollectorContract;
use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Utils\Contracts\BiConsumer;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template E of mixed
 * @template C of \Smpl\Collections\Contracts\Collection
 *
 * @implements \Smpl\Collections\Contracts\Collector<E, C>
 */
final class BasicCombiningCollector implements CollectorContract
{
    /**
     * @param class-string $collectionClass
     *
     * @return \Smpl\Collections\Collectors\BasicCombiningCollector<mixed, \Smpl\Collections\Contracts\Collection>
     */
    public static function for(string $collectionClass): BasicCombiningCollector
    {
        if (! is_subclass_of($collectionClass, Collection::class)) {
            throw InvalidArgumentException::collectorNoCollection();
        }

        /** @psalm-suppress InvalidArgument */
        return new self(
            new Collector(
                supplier: \Smpl\Utils\supplier($collectionClass::of(...)),
                combiner: \Smpl\Utils\biConsumer(self::addAllToCollection(...))
            )
        );
    }

    /**
     * @var \Smpl\Collections\Collectors\Collector<E, C>
     */
    private Collector $collector;

    /**
     * @param \Smpl\Collections\Collectors\Collector<E, C> $collector
     */
    private function __construct(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @template NE of mixed
     * @template NC of \Smpl\Collections\Contracts\Collection
     *
     * @param NC           $collection
     * @param iterable<NE> $elements
     *
     * @return void
     */
    private static function addAllToCollection(Collection $collection, iterable $elements): void
    {
        $collection->addAll($elements);
    }

    /**
     * @return \Smpl\Utils\Contracts\Supplier<C>
     */
    public function supplier(): Supplier
    {
        return $this->collector->supplier();
    }

    /**
     * @return \Smpl\Utils\Contracts\BiConsumer<C, E>|null
     */
    public function accumulator(): ?BiConsumer
    {
        return $this->collector->accumulator();
    }

    /**
     * @return \Smpl\Utils\Contracts\BiConsumer<C, iterable<E>>|null
     */
    public function combiner(): ?BiConsumer
    {
        return $this->collector->combiner();
    }
}