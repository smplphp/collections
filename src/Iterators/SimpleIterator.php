<?php
declare(strict_types=1);

namespace Smpl\Collections\Iterators;

use Iterator;

/**
 * Simple Iterator
 *
 * This is a simple, straight-forward iterate allowing you to iterate over an
 * array in a way that doesn't allow modifying of the underlying array.
 *
 * @template I of mixed
 * @template E of mixed
 * @implements \Iterator<I, E>
 * @psalm-immutable
 */
final class SimpleIterator implements Iterator
{
    /**
     * @var array<I, E>
     */
    private array $elements;

    /**
     * @param array<I, E> $elements
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return E|false
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     *
     * @uses           \current()
     */
    public function current(): mixed
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return current($this->elements);
    }

    /**
     * @return void
     *
     * @uses \next()
     */
    public function next(): void
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        next($this->elements);
    }

    /**
     * @return I|null
     *
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     *
     * @uses         \key()
     */
    public function key(): mixed
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return key($this->elements);
    }

    /**
     * @return bool
     *
     * @uses \Smpl\Collections\Iterators\SimpleIterator::key()
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * @return void
     *
     * @uses \reset()
     */
    public function rewind(): void
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        reset($this->elements);
    }
}