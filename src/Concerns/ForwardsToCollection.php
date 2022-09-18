<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\CollectionMutable;
use Smpl\Collections\Contracts\Predicate;
use Traversable;

/**
 * Forwards to Collection Concern
 *
 * This trait allows you to create decorators for {@see \Smpl\Collections\Contracts\CollectionMutable}
 * instances, requiring only the definition of the {@see \Smpl\Collections\Concerns\ForwardsToCollection::delegate()}
 * method and {@see \Smpl\Collections\Contracts\CollectionMutable::copy()}.
 *
 * @requires \Smpl\Collections\Contracts\CollectionMutable
 * @mixin \Smpl\Collections\Contracts\CollectionMutable
 * @template E of mixed
 * @template-implements \Smpl\Collections\Contracts\CollectionMutable<E>
 */
trait ForwardsToCollection
{
    /**
     * Get the delegate collection.
     *
     * @return \Smpl\Collections\Contracts\CollectionMutable<E>
     */
    abstract protected function delegate(): CollectionMutable;

    /**
     * @param E $element
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::contains()
     */
    public function contains(mixed $element): bool
    {
        return $this->delegate()->contains($element);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::containsAll()
     */
    public function containsAll(iterable $elements): bool
    {
        return $this->delegate()->containsAll($elements);
    }

    /**
     * @return bool
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::isEmpty()
     */
    public function isEmpty(): bool
    {
        return $this->delegate()->isEmpty();
    }

    /**
     * @return int
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::count()
     */
    public function count(): int
    {
        return $this->delegate()->count();
    }

    /**
     * @return list<E>
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::toArray()
     */
    public function toArray(): array
    {
        return $this->delegate()->toArray();
    }

    /**
     * @param E $element
     *
     * @return static
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::add()
     */
    public function add(mixed $element): static
    {
        $this->delegate()->add($element);

        return $this;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::addAll()
     */
    public function addAll(iterable $elements): static
    {
        $this->delegate()->addAll($elements);

        return $this;
    }

    /**
     * @return static
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::clear()
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
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::remove()
     */
    public function remove(mixed $element): bool
    {
        return $this->delegate()->remove($element);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::removeAll()
     */
    public function removeAll(iterable $elements): bool
    {
        return $this->delegate()->removeAll($elements);
    }

    /**
     * @param \Smpl\Collections\Contracts\Predicate<E>|callable(E):bool $criteria
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::removeIf()
     */
    public function removeIf(callable|Predicate $criteria): bool
    {
        return $this->delegate()->removeIf($criteria);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::retainAll()
     */
    public function retainAll(iterable $elements): bool
    {
        return $this->delegate()->retainAll($elements);
    }

    /**
     * @return \Traversable<int, E>
     *
     * @throws \Exception
     *
     * @uses \Smpl\Collections\Concerns\ForwardsToCollection::delegate()::getIterator()
     */
    public function getIterator(): Traversable
    {
        return $this->delegate()->getIterator();
    }
}