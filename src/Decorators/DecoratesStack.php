<?php
declare(strict_types=1);

namespace Smpl\Collections\Decorators;

use Smpl\Collections\Contracts\Stack;

/**
 * Stack Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\Stack} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Sequence decorators will need to implement the following three methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesStack::delegate()}
 *  - {@see \Smpl\Collections\Contracts\Collection::of()}
 *  - {@see \Smpl\Collections\Contracts\Collection::copy()}
 *
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Stack<E>
 */
trait DecoratesStack
{
    use DecoratesCollection;

    /**
     * Get the delegate collection.
     *
     * This method allows this concern to proxy method calls to a delegate
     * collection provided by this method.
     *
     * @return \Smpl\Collections\Contracts\Stack<E>
     */
    abstract protected function delegate(): Stack;

    /**
     * @return E|null
     */
    public function peekLast(): mixed
    {
        return $this->delegate()->peekLast();
    }

    /**
     * @return E|null
     */
    public function pollLast(): mixed
    {
        return $this->delegate()->pollLast();
    }
}