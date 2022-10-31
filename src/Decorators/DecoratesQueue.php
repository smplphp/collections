<?php
declare(strict_types=1);

namespace Smpl\Collections\Decorators;

use Smpl\Collections\Contracts\Queue;

/**
 * Queue Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\Queue} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Sequence decorators will need to implement the following three methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesQueue::delegate()}
 *  - {@see \Smpl\Collections\Contracts\Collection::of()}
 *  - {@see \Smpl\Collections\Contracts\Collection::copy()}
 *
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Queue<E>
 */
trait DecoratesQueue
{
    use DecoratesCollection;

    /**
     * Get the delegate collection.
     *
     * This method allows this concern to proxy method calls to a delegate
     * collection provided by this method.
     *
     * @return \Smpl\Collections\Contracts\Queue<E>
     */
    abstract protected function delegate(): Queue;

    /**
     * @return E|null
     */
    public function peekFirst(): mixed
    {
        return $this->delegate()->peekFirst();
    }

    /**
     * @return E|null
     */
    public function pollFirst(): mixed
    {
        return $this->delegate()->pollFirst();
    }
}