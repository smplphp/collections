<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\SortsCollection;
use Smpl\Collections\Contracts\Set;
use Smpl\Collections\Contracts\SortedCollection as SortedCollectionContract;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\SortedCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Set<E>
 */
final class SortedSet extends BaseCollection implements SortedCollectionContract, Set
{
    use SortsCollection;

    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\SortedSet<NE>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     *
     * @noinspection   PhpDocSignatureInspection
     * @noinspection   PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null): static
    {
        $elements ??= $this->elements;

        return new SortedSet($elements, $this->getComparator());
    }

    /**
     * Ensure that this collection contains the provided element.
     *
     * This method will function the same as {@see \Smpl\Collections\BaseCollection::add()}
     * with the exception that it will check for the elements' presence, to make
     * sure this set only contains unique values.
     *
     * @param E $element
     *
     * @return bool
     */
    public function add(mixed $element): bool
    {
        if ($this->contains($element)) {
            return false;
        }

        return parent::add($element);
    }
}