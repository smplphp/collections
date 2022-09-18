<?php
declare(strict_types=1);

namespace Smpl\Collections;

use ArrayIterator;
use Smpl\Collections\Contracts\Predicate;
use Smpl\Collections\Support\Collections;
use Smpl\Collections\Support\Predicates;
use Traversable;

/**
 * Collection
 *
 * A base implementation providing an iterable mutable interface for a linear collection
 * of elements.
 *
 * @template E of mixed
 * @template-implements \Smpl\Collections\Contracts\CollectionMutable<E>
 */
class Collection implements Contracts\CollectionMutable
{
    /**
     * @var list<E>
     */
    protected array $elements = [];

    /**
     * @param iterable<E> $elements
     */
    public function __construct(iterable $elements = [])
    {
        $this->addAll($elements);
    }

    /**
     * @param E $element
     *
     * @return bool
     *
     * @uses \in_array()
     */
    public function contains(mixed $element): bool
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Collection::contains()
     */
    public function containsAll(iterable $elements): bool
    {
        foreach ($elements as $element) {
            if (! $this->contains($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     *
     * @uses \empty()
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @return int
     *
     * @uses \count()
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @return list<E>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @param E $element
     *
     * @return static
     */
    public function add(mixed $element): static
    {
        $this->elements[] = $element;
        return $this;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @uses \Smpl\Collections\Collection::add()
     */
    public function addAll(iterable $elements): static
    {
        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function clear(): static
    {
        $this->elements = [];
        return $this;
    }

    /**
     * @param E $element
     *
     * @return bool
     *
     * @uses \array_search()
     * @uses \unset()
     */
    public function remove(mixed $element): bool
    {
        $found = false;

        do {
            $index = array_search($element, $this->elements, true);

            if ($index !== false) {
                unset($this->elements[$index]);
                $found = true;
            }
        } while ($index !== false);

        return $found;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Collection::remove()
     */
    public function removeAll(iterable $elements): bool
    {
        $changed = false;

        foreach ($elements as $element) {
            if ($this->remove($element)) {
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * @param \Smpl\Collections\Contracts\Predicate<E>|callable(E):bool $criteria
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Collection::remove()
     */
    public function removeIf(callable|Predicate $criteria): bool
    {
        $changed = false;

        foreach ($this->elements as $element) {
            if ($criteria($element) && $this->remove($element)) {
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Collection::removeIf()
     * @uses \Smpl\Collections\Support\Predicates::contains()
     * @uses \Smpl\Collections\Predicates\ContainsPredicate
     * @uses \Smpl\Collections\Support\Collections::collectImmutable()
     * @uses \Smpl\Collections\ImmutableCollection
     */
    public function retainAll(iterable $elements): bool
    {
        return $this->removeIf(
            Predicates::contains(Collections::collectImmutable($elements))
        );
    }

    /**
     * @return static<E>
     *
     * @psalm-suppress UnsafeInstantiation
     */
    public function copy(): static
    {
        return new static($this->elements);
    }

    /**
     * @return \Traversable<int, E>
     *
     * @uses \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }
}