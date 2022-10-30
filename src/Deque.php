<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Iterator;
use Smpl\Collections\Iterators\SimpleIterator;

/**
 * Deque
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
 * These methods are provided by this contract in an attempt
 * to provide some consistency. Each of these methods, either through the
 * Queue, Stack or this contract will have a variation suffixed with "last"
 * or "first" for more controlled handling.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 * @implements \Smpl\Collections\Contracts\Deque<E>
 */
final class Deque extends BaseCollection implements Contracts\Deque
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Deque<NE>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     *
     * @noinspection   PhpDocSignatureInspection
     * @noinspection   PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null): static
    {
        return new Deque($elements ?? $this->elements, $this->getComparator());
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
    public function peekLast(): mixed
    {
        return $this->elements[$this->getMaxIndex()] ?? null;
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
     * @return E|null
     */
    public function peek(): mixed
    {
        return $this->peekFirst();
    }

    /**
     * @return E|null
     */
    public function poll(): mixed
    {
        return $this->pollFirst();
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
}