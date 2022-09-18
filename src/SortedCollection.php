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
     * @param iterable<E>                                                       $elements
     * @param \Smpl\Collections\Contracts\Comparator<E>|callable(E, E):int|null $comparator
     *
     * @uses \Smpl\Collections\Support\Comparators::ensureInstance()
     * @uses \Smpl\Collections\Comparators\DefaultComparator
     */
    public function __construct(iterable $elements = [], Comparator|callable|null $comparator = null)
    {
        $this->comparator = Comparators::ensureInstance($comparator);
        parent::__construct($elements);
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
            /** @infection-ignore-all  */
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

    /**
     * Get the comparator the collection is using.
     *
     * @return \Smpl\Collections\Contracts\Comparator<E>
     */
    public function getComparator(): Contracts\Comparator
    {
        return $this->comparator;
    }
}