<?php
declare(strict_types=1);

namespace Smpl\Collections\Collectors;

use Smpl\Collections\Contracts\Collector as CollectorContract;
use Smpl\Utils\Contracts\BiConsumer;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template E of mixed
 * @template C of \Smpl\Collections\Contracts\Collection
 *
 * @implements \Smpl\Collections\Contracts\Collector<E, C>
 */
final class Collector implements CollectorContract
{
    /**
     * @template NE of mixed
     * @template NC of \Smpl\Collections\Contracts\Collection
     *
     * @param \Smpl\Utils\Contracts\Supplier<NC>                      $supplier
     * @param \Smpl\Utils\Contracts\BiConsumer<NC, NE>|null           $accumulator
     * @param \Smpl\Utils\Contracts\BiConsumer<NC, iterable<NE>>|null $combiner
     *
     * @return \Smpl\Collections\Collectors\Collector<NE, NC>
     */
    public static function of(Supplier $supplier, ?BiConsumer $accumulator = null, ?BiConsumer $combiner = null): Collector
    {
        return new self($supplier, $accumulator, $combiner);
    }

    /**
     * @var \Smpl\Utils\Contracts\Supplier<C>
     */
    private Supplier $supplier;

    /**
     * @var \Smpl\Utils\Contracts\BiConsumer<C, E>|null
     */
    private ?BiConsumer $accumulator;

    /**
     * @var \Smpl\Utils\Contracts\BiConsumer<C, iterable<E>>|null
     */
    private ?BiConsumer $combiner;

    /**
     * @param \Smpl\Utils\Contracts\Supplier<C>                     $supplier
     * @param \Smpl\Utils\Contracts\BiConsumer<C, E>|null           $accumulator
     * @param \Smpl\Utils\Contracts\BiConsumer<C, iterable<E>>|null $combiner
     */
    public function __construct(Supplier $supplier, ?BiConsumer $accumulator = null, ?BiConsumer $combiner = null)
    {
        $this->supplier    = $supplier;
        $this->accumulator = $accumulator;
        $this->combiner    = $combiner;
    }

    /**
     * @return \Smpl\Utils\Contracts\Supplier<C>
     */
    public function supplier(): Supplier
    {
        return $this->supplier;
    }

    /**
     * @return \Smpl\Utils\Contracts\BiConsumer<C, E>|null
     */
    public function accumulator(): ?BiConsumer
    {
        return $this->accumulator;
    }

    /**
     * @return \Smpl\Utils\Contracts\BiConsumer<C, iterable<E>>|null
     */
    public function combiner(): ?BiConsumer
    {
        return $this->combiner;
    }
}