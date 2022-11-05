<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Iterator;
use Smpl\Collections\Iterators\SimpleIterator;

/**
 * Base Deque
 *
 * A deque, which stands for double-ended-queue and is pronounced deck. A Deque
 * functions as both {@see \Smpl\Collections\Contracts\Queue} and
 * {@see \Smpl\Collections\Contracts\Stack}.
 *
 * For the purpose of the following methods, this collection will behave like a
 * {@see \Smpl\Collections\Contracts\Queue}.
 *
 *   - {@see \Smpl\Collections\Contracts\Deque::peek()}
 *   - {@see \Smpl\Collections\Contracts\Deque::poll()}
 *
 * This class forms the base of the other Deque classes.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Deque<E>
 */
abstract class BaseDeque extends BaseCollection implements Contracts\Deque
{
    /**
     * @param E $element
     *
     * @return bool
     */
    public function addFirst(mixed $element): bool
    {
        array_unshift($this->elements, $element);

        return true;
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function addLast(mixed $element): bool
    {
        return $this->add($element);
    }

    /**
     * @return \Smpl\Collections\Contracts\Queue
     */
    public function asQueue(): Contracts\Queue
    {
        return new Queue($this->elements, $this->getComparator());
    }

    /**
     * @return \Smpl\Collections\Contracts\Stack
     */
    public function asStack(): Contracts\Stack
    {
        return new Stack($this->elements, $this->getComparator());
    }

    /**
     * @return \Iterator<int, E>
     */
    public function ascendingIterator(): Iterator
    {
        return new SimpleIterator($this->elements);
    }

    /**
     * @return \Iterator<int, E>
     */
    public function descendingIterator(): Iterator
    {
        return new SimpleIterator(array_reverse($this->elements));
    }

    /**
     * @return E|null
     */
    public function peek(): mixed
    {
        return $this->peekFirst();
    }

    /**
     * @return E|null
     */
    public function peekFirst(): mixed
    {
        return $this->elements[0] ?? null;
    }

    /**
     * @return E|null
     */
    public function peekLast(): mixed
    {
        return $this->elements[$this->getMaxIndex()] ?? null;
    }

    /**
     * @return E|null
     */
    public function poll(): mixed
    {
        return $this->pollFirst();
    }

    /**
     * @return E|null
     */
    public function pollFirst(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $element = array_shift($this->elements);

        $this->modifyCount(-1);

        return $element;
    }

    /**
     * @return E|null
     */
    public function pollLast(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $element = array_pop($this->elements);

        $this->modifyCount(-1);

        return $element;
    }
}