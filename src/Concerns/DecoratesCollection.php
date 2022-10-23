<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\Collection;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Traversable;

/**
 * Collection Decorator Concern
 *
 * This concern exists as a helper for decorating the
 * {@see \Smpl\Collections\Contracts\Collection} contract. Adding this to
 * a class will create a proxy class that delegates method calls to an
 * implementation provided by the implementor.
 *
 * Collection decorators will need to implement the following three methods:
 *
 *  - {@see \Smpl\Collections\Concerns\DecoratesCollection::delegate()}
 *  - {@see \Smpl\Collections\Contracts\Collection::of()}
 *  - {@see \Smpl\Collections\Contracts\Collection::copy()}
 *
 * @template I of mixed
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Collection<I, E>
 */
trait DecoratesCollection
{
    /**
     * The mutable collection method calls should be delegated too.
     *
     * This method allows this concern
     *
     * @return \Smpl\Collections\Contracts\Collection<I, E>
     */
    abstract protected function delegate(): Collection;

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
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
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
     * @return \Smpl\Utils\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->delegate()->getComparator();
    }

    /**
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return static
     */
    public function setComparator(?Comparator $comparator = null): static
    {
        $this->delegate()->setComparator($comparator);

        return $this;
    }
}