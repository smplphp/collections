<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\Sequence;
use Smpl\Collections\Contracts\Set;

/**
 * Sequence Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\Sequence} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Sequence decorators will need to implement the following three methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesSequence::delegate()}
 *  - {@see \Smpl\Collections\Contracts\Collection::of()}
 *  - {@see \Smpl\Collections\Contracts\Collection::copy()}
 *
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Sequence<E>
 */
trait DecoratesSequence
{
    use DecoratesCollection;

    /**
     * Get the delegate collection.
     *
     * This method allows this concern to proxy method calls to a delegate
     * collection provided by this method.
     *
     * @return \Smpl\Collections\Contracts\Sequence<E>
     */
    abstract protected function delegate(): Sequence;

    /**
     * @param E   $element
     * @param int $index
     *
     * @return int|null
     */
    public function find(mixed $element, int $index): ?int
    {
        return $this->delegate()->find($element, $index);
    }

    /**
     * @return E
     */
    public function first(): mixed
    {
        return $this->delegate()->first();
    }

    /**
     * @param int $index
     *
     * @return E|null
     */
    public function get(int $index): mixed
    {
        return $this->delegate()->get($index);
    }

    /**
     * @param int $index
     *
     * @return bool
     */
    public function has(int $index): bool
    {
        return $this->delegate()->has($index);
    }

    /**
     * @param E $element
     *
     * @return int|null
     */
    public function indexOf(mixed $element): ?int
    {
        return $this->delegate()->indexOf($element);
    }

    /**
     * @param E $element
     *
     * @return \Smpl\Collections\Contracts\Set<E>
     */
    public function indexesOf(mixed $element): Set
    {
        return $this->delegate()->indexesOf($element);
    }

    /**
     * @param E $element
     *
     * @return int|null
     */
    public function lastIndexOf(mixed $element): ?int
    {
        return $this->delegate()->lastIndexOf($element);
    }

    /**
     * @return E
     */
    public function last(): mixed
    {
        return $this->delegate()->last();
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->delegate()->offsetExists($offset);
    }

    /**
     * @param int $offset
     *
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->delegate()->offsetGet($offset);
    }

    /**
     * @param int $offset
     * @param E   $value
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->delegate()->offsetSet($offset, $value);
    }

    /**
     * @param int $offset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->delegate()->offsetUnset($offset);
    }

    /**
     * @param int $index
     * @param E   $element
     *
     * @return static
     */
    public function put(int $index, mixed $element): static
    {
        $this->delegate()->put($index, $element);

        return $this;
    }

    /**
     * @param int         $index
     * @param iterable<E> $elements
     *
     * @return static
     */
    public function putAll(int $index, iterable $elements): static
    {
        $this->delegate()->putAll($index, $elements);

        return $this;
    }

    /**
     * @param int $index
     * @param E   $element
     *
     * @return static
     */
    public function set(int $index, mixed $element): static
    {
        $this->delegate()->set($index, $element);

        return $this;
    }

    /**
     * @param int         $index
     * @param iterable<E> $elements
     *
     * @return static
     */
    public function setAll(int $index, iterable $elements): static
    {
        $this->delegate()->setAll($index, $elements);

        return $this;
    }

    /**
     * @param int      $index
     * @param int|null $length
     *
     * @return static
     */
    public function subset(int $index, int $length = null): static
    {
        return $this->copy($this->delegate()->subset($index, $length));
    }

    /**
     * @return static
     */
    public function tail(): static
    {
        return $this->copy($this->delegate()->tail());
    }

    /**
     * @param int $index
     *
     * @return $this
     */
    public function unset(int $index): static
    {
        $this->delegate()->unset($index);

        return $this;
    }
}