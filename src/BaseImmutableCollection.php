<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Comparator;
use Smpl\Collections\Helpers\IterableHelper;
use Smpl\Collections\Iterators\SimpleIterator;
use Traversable;

/**
 * Base Immutable Collection
 *
 * This class forms the base for immutable collections, preventing
 * the need to duplicate code and method definitions where inheritance will
 * suffice.
 *
 * @template E of mixed
 * @implements \Smpl\Collections\Contracts\Collection<int, E>
 * @psalm-immutable
 */
abstract class BaseImmutableCollection implements Collection
{
    /**
     * @var list<E>
     */
    protected readonly array $elements;

    /**
     * @var int<0, max>
     */
    protected readonly int $count;

    /**
     * @var \Smpl\Collections\Contracts\Comparator<E>|null
     */
    protected readonly ?Comparator $comparator;

    /**
     * @param iterable<E> $elements
     */
    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->comparator = $comparator;

        if (is_array($elements)) {
            // If the provided elements is just an array, we can
            // set its values as the current elements, and just count that.
            /** @infection-ignore-all */
            $newElements = array_values($elements);
            $count       = count($newElements);
        } else {
            // If the provided elements are not in an array, we'll need to
            // iterable over them and build the count up as we go.
            $newElements = [];
            $count       = 0;

            foreach ($elements as $element) {
                $newElements[] = $element;
                $count++;
            }
        }

        // These are set here because PHPStan isn't smart enough to know that
        // only one of the above if/else sections will ever be reached at any
        // one time.
        $this->elements = $newElements;
        $this->count    = $count;
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return IterableHelper::contains($this->elements, $element, $this->comparator);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return IterableHelper::containsAll($this->elements, $elements, $this->comparator);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return \Traversable<int, E>
     */
    public function getIterator(): Traversable
    {
        return new SimpleIterator($this->toArray());
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @param mixed $element
     *
     * @return int<0, max>
     */
    public function countOf(mixed $element): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return IterableHelper::countOf($this->elements, $element, $this->comparator);
    }

    /**
     * @return list<E>
     */
    public function toArray(): array
    {
        /**
         * The combined effort of this call and the `array_values` call in
         * the constructor means that this will always return a list, but we
         * have to have this here for static analysis and mutation testing.
         *
         * @psalm-suppress RedundantFunctionCall
         * @infection-ignore-all
         */
        return array_values($this->elements);
    }

    /**
     * @return \Smpl\Collections\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->comparator;
    }
}