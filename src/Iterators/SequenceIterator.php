<?php
declare(strict_types=1);

namespace Smpl\Collections\Iterators;

use Countable;
use Iterator;
use SeekableIterator;
use Smpl\Collections\Contracts\Sequence;
use Smpl\Collections\Exceptions\OutOfRangeException;

/**
 * Sequence Iterator
 *
 * This iterator is special implementation specifically for iterating over
 * sequences, providing proxy functionality for the various mutable methods
 * contained within {@see \Smpl\Collections\Contracts\Sequence}, that use
 * the current index.
 *
 * This iterator also provides a few navigation methods for jumping around the
 * iterator, most notable {@see \SeekableIterator::seek()}, but also a wrapper
 * for {@see \Smpl\Collections\Contracts\Sequence::find()} that allows you
 * to jump to the next occurrence of an element.
 *
 * @template E of mixed
 * @implements \Iterator<int, E>
 */
final class SequenceIterator implements Iterator, SeekableIterator, Countable
{
    /**
     * @var \Smpl\Collections\Contracts\Sequence<E>
     */
    private Sequence $sequence;

    /**
     * @var int<0, max>
     */
    private int $index = 0;

    /**
     * @param \Smpl\Collections\Contracts\Sequence<E> $sequence
     */
    public function __construct(Sequence $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return E|null
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function current(): mixed
    {
        return $this->sequence->get($this->index);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        ++$this->index;
    }

    /**
     * @return int<0, max>|null
     */
    public function key(): ?int
    {
        if ($this->index > $this->getMaxIndex()) {
            return null;
        }

        return $this->index;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->sequence->has($this->index);
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * @param int $offset
     *
     * @return void
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function seek(int $offset): void
    {
        /** @infection-ignore-all  */
        if ($offset < 0 || $offset >= $this->sequence->count()) {
            throw OutOfRangeException::index($offset, 0, $this->getMaxIndex());
        }

        /** @psalm-suppress PropertyTypeCoercion */
        $this->index = $offset;
    }

    /**
     * Put an element at the current index.
     *
     * This method is a proxy for {@see \Smpl\Collections\Contracts\Sequence::put()}
     * that uses the current index of the iterator.
     *
     * @param E $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function put(mixed $element): self
    {
        if ($this->key() === null) {
            throw OutOfRangeException::index($this->index, 0, $this->getMaxIndex());
        }

        /** @psalm-suppress PossiblyNullArgument */
        $this->sequence->put($this->key(), $element);
        return $this;
    }

    /**
     * Put all elements at the current index.
     *
     * This method is a proxy for {@see \Smpl\Collections\Contracts\Sequence::putAll()}
     * that uses the current index of the iterator.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function putAll(iterable $elements): self
    {
        if ($this->key() === null) {
            throw OutOfRangeException::index($this->index, 0, $this->getMaxIndex());
        }

        /** @psalm-suppress PossiblyNullArgument */
        $this->sequence->putAll($this->key(), $elements);
        return $this;
    }

    /**
     * Put an element at the current index, replacing what is currently there.
     *
     * This method is a proxy for {@see \Smpl\Collections\Contracts\Sequence::set()}
     * that uses the current index of the iterator.
     *
     * @param E $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function set(mixed $element): self
    {
        if ($this->key() === null) {
            throw OutOfRangeException::index($this->index, 0, $this->getMaxIndex());
        }

        /** @psalm-suppress PossiblyNullArgument */
        $this->sequence->set($this->key(), $element);
        return $this;
    }

    /**
     * Put all elements at the current index, replacing what is currently there.
     *
     * This method is a proxy for {@see \Smpl\Collections\Contracts\Sequence::setAll()}
     * that uses the current index of the iterator.
     *
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function setAll(iterable $elements): self
    {
        if ($this->key() === null) {
            throw OutOfRangeException::index($this->index, 0, $this->getMaxIndex());
        }

        /** @psalm-suppress PossiblyNullArgument */
        $this->sequence->setAll($this->key(), $elements);
        return $this;
    }

    /**
     * Find the next occurrence of the provided element and jump to it.
     *
     * This method uses {@see \Smpl\Collections\Contracts\Sequence::find()} with
     * the iterators current index, to find the next occurrence of the provided
     * element, and jump to it.
     *
     * @param E $element
     *
     * @return bool
     */
    public function find(mixed $element): bool
    {
        $index = $this->sequence->find($element, $this->index);

        if ($index === null) {
            return false;
        }

        $this->index = $index;
        return true;
    }

    /**
     * Unset the current element.
     *
     * This method uses {@see \Smpl\Collections\Contracts\Sequence::unset()} with
     * the current index of the iterator.
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function unset(): self
    {
        $this->sequence->unset($this->index);
        return $this;
    }

    public function count(): int
    {
        return $this->sequence->count();
    }

    /**
     * Get the max index for the sequence this iterator represents.
     *
     * @return int<0, max>
     */
    private function getMaxIndex(): int
    {
        return max(0, ($this->count() - 1));
    }
}