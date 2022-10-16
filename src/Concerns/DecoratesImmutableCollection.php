<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Comparator;
use Traversable;

/**
 * @template I of mixed
 * @template E of mixed
 * @mixin \Smpl\Collections\Contracts\Collection<I, E>
 */
trait DecoratesImmutableCollection
{
    /**
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
     * @return \Smpl\Collections\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->delegate()->getComparator();
    }
}