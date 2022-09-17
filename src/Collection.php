<?php
declare(strict_types=1);

namespace Smpl\Collections;

use ArrayIterator;
use Smpl\Collections\Contracts\Predicate;
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
    private array $elements = [];

    /**
     * @param iterable<E> $elements
     */
    public function __construct(iterable $elements)
    {
        $this->addAll($elements);
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
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
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @return int
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
     * @return $this
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
     */
    public function addAll(iterable $elements): static
    {
        foreach ($elements as $element) {
            $this->elements[] = $element;
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
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }
}