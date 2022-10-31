<?php
declare(strict_types=1);

namespace Smpl\Collections\Decorators;

use Smpl\Collections\Contracts\Deque;
use Smpl\Collections\Contracts\Queue;
use Smpl\Collections\Contracts\Stack;
use Iterator;

/**
 * Stack Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\Deque} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Sequence decorators will need to implement the following three methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesDeque::delegate()}
 *  - {@see \Smpl\Collections\Contracts\Collection::of()}
 *  - {@see \Smpl\Collections\Contracts\Collection::copy()}
 *
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Deque<E>
 */
trait DecoratesDeque
{
    use DecoratesCollection,
        DecoratesQueue,
        DecoratesStack;

    /**
     * Get the delegate collection.
     *
     * This method allows this concern to proxy method calls to a delegate
     * collection provided by this method.
     *
     * @return \Smpl\Collections\Contracts\Deque<E>
     */
    abstract protected function delegate(): Deque;

    /**
     * @param E $element
     *
     * @return bool
     */
    public function addFirst(mixed $element): bool
    {
        return $this->delegate()->addFirst($element);
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function addLast(mixed $element): bool
    {
        return $this->delegate()->addLast($element);
    }

    /**
     * @return \Smpl\Collections\Contracts\Queue<E>
     */
    public function asQueue(): Queue
    {
        return $this->delegate()->asQueue();
    }

    /**
     * @return \Smpl\Collections\Contracts\Stack<E>
     */
    public function asStack(): Stack
    {
        return $this->delegate()->asStack();
    }

    /**
     * @return E|null
     */
    public function peek(): mixed
    {
        return $this->delegate()->peek();
    }

    /**
     * @return E|null
     */
    public function poll(): mixed
    {
        return $this->delegate()->poll();
    }

    /**
     * @return \Iterator<int, E>
     */
    public function ascendingIterator(): Iterator
    {
        return $this->delegate()->ascendingIterator();
    }

    /**
     * @return \Iterator<int, E>
     */
    public function descendingIterator(): Iterator
    {
        return $this->delegate()->descendingIterator();
    }
}