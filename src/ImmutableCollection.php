<?php
declare(strict_types=1);

namespace Smpl\Collections;

use ArrayIterator;
use Traversable;

/**
 * Immutable Collection
 *
 * A base implementation providing an iterable immutable interface for a linear collection
 * of elements.
 *
 * @template E of mixed
 * @template-implements \Smpl\Collections\Contracts\Collection<E>
 */
class ImmutableCollection implements Contracts\Collection
{
    /**
     * @var list<E>
     */
    private array $elements = [];

    /**
     * @param iterable<E> $elements
     */
    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $element) {
            $this->elements[] = $element;
        }
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
     * @return \Traversable<int, E>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }
}