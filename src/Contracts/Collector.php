<?php

namespace Smpl\Collections\Contracts;

use Smpl\Utils\Contracts\BiConsumer;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template E of mixed
 * @template C of \Smpl\Collections\Contracts\Collection
 */
interface Collector
{
    /**
     * @return \Smpl\Utils\Contracts\Supplier<C>
     */
    public function supplier(): Supplier;

    /**
     * @return \Smpl\Utils\Contracts\BiConsumer<C, E>|null
     */
    public function accumulator(): ?BiConsumer;

    /**
     * @return \Smpl\Utils\Contracts\BiConsumer<C, iterable<E>>|null
     */
    public function combiner(): ?BiConsumer;
}