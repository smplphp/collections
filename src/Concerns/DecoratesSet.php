<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\Set;

/**
 * Set Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\set} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Sequence decorators will need to implement the following three methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesSet::delegate()}
 *  - {@see \Smpl\Collections\Contracts\Collection::of()}
 *  - {@see \Smpl\Collections\Contracts\Collection::copy()}
 *
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Set<E>
 */
trait DecoratesSet
{
    use DecoratesCollection;

    /**
     * Get the delegate collection.
     *
     * This method allows this concern to proxy method calls to a delegate
     * collection provided by this method.
     *
     * @return \Smpl\Collections\Contracts\Set<E>
     */
    abstract protected function delegate(): Set;
}