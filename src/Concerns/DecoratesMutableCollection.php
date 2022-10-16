<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\Comparator;
use Smpl\Collections\Contracts\MutableCollection;
use Smpl\Collections\Contracts\Predicate;
use Traversable;

/**
 * Mutable Collection Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\MutableCollection} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Mutable collection decorators will need to implement the following three
 * methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesMutableCollection::delegate()}
 *  - {@see \Smpl\Collections\Contracts\MutableCollection::of()}
 *  - {@see \Smpl\Collections\Contracts\MutableCollection::copy()}
 *
 * @template I of mixed
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\MutableCollection<I, E>
 */
trait DecoratesMutableCollection
{
    /**
     * The mutable collection method calls should be delegated too.
     *
     * This method allows this concern
     *
     * @return \Smpl\Collections\Contracts\MutableCollection<I, E>
     */
    abstract protected function delegate(): MutableCollection;

    /**
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool
    {
        return $this->delegate()->contains($element);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool
    {
        return $this->delegate()->containsAll($elements);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->delegate()->isEmpty();
    }

    /**
     * @return \Traversable<I, E>
     * @throws \Exception
     */
    public function getIterator(): Traversable
    {
        return $this->delegate()->getIterator();
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->delegate()->count();
    }

    /**
     * @param E $element
     *
     * @return int<0, max>
     */
    public function countOf(mixed $element): int
    {
        return $this->delegate()->countOf($element);
    }

    /**
     * @return list<E>
     */
    public function toArray(): array
    {
        return $this->delegate()->toArray();
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function add(mixed $element): bool
    {
        return $this->delegate()->add($element);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function addAll(iterable $elements): bool
    {
        return $this->delegate()->addAll($elements);
    }

    /**
     * @return static
     */
    public function clear(): static
    {
        $this->delegate()->clear();
        return $this;
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function remove(mixed $element): bool
    {
        return $this->delegate()->remove($element);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function removeAll(iterable $elements): bool
    {
        return $this->delegate()->removeAll($elements);
    }

    /**
     * @param \Smpl\Collections\Contracts\Predicate<E> $filter
     *
     * @return bool
     */
    public function removeIf(Predicate $filter): bool
    {
        return $this->delegate()->removeIf($filter);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function retainAll(iterable $elements): bool
    {
        return $this->delegate()->retainAll($elements);
    }

    /**
     * @return \Smpl\Collections\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->delegate()->getComparator();
    }

    /**
     * @param \Smpl\Collections\Contracts\Comparator<E>|null $comparator
     *
     * @return static
     */
    public function setComparator(?Comparator $comparator = null): static
    {
        $this->delegate()->setComparator($comparator);

        return $this;
    }
}