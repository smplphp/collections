<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Contracts\Comparator;
use Smpl\Collections\Support\Comparators;

/**
 * Sorted Collection
 *
 * An extension of {@see \Smpl\Collections\Collection} that automatically sorts
 * its elements based on a provided {@see \Smpl\Collections\Contracts\Comparator},
 * or {@see \Smpl\Collections\Comparators\DefaultComparator}, every time an element
 * is added or removed.
 *
 * @template E of mixed
 * @template-extends \Smpl\Collections\Collection<E>
 */
class SortedCollection extends Collection
{
    /**
     * @var \Smpl\Collections\Contracts\Comparator<E>
     */
    private $comparator;

    /**
     * @param \Smpl\Collections\Contracts\Comparator<E>|callable(E, E):int|null $comparator
     * @param iterable<E>                                                       $elements
     *
     * @uses \Smpl\Collections\Support\Comparators::ensureInstance()
     * @uses \Smpl\Collections\Comparators\DefaultComparator
     */
    public function __construct(Comparator|callable|null $comparator = null, iterable $elements = [])
    {
        parent::__construct($elements);
        $this->comparator = Comparators::ensureInstance($comparator);
    }

    /**
     * @param E $element
     *
     * @return static
     *
     * @uses \Smpl\Collections\Collection::add()
     * @uses \Smpl\Collections\SortedCollection::sort()
     */
    public function add(mixed $element): static
    {
        parent::add($element);

        $this->sort();

        return $this;
    }

    /**
     * @param E $element
     *
     * @return bool
     *
     * @uses \Smpl\Collections\Collection::remove()
     * @uses \Smpl\Collections\SortedCollection::sort()
     */
    public function remove(mixed $element): bool
    {
        if (parent::remove($element)) {
            $this->sort();

            return true;
        }

        return false;
    }

    /**
     * @return void
     *
     * @uses \usort()
     */
    private function sort(): void
    {
        usort($this->elements, $this->comparator);
    }
}