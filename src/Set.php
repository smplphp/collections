<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * Set
 *
 * A mutable base collection, exactly like {@see \Smpl\Collections\Collection},
 * except that it does not allow duplicates.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Set<E>
 */
final class Set extends BaseCollection implements Contracts\Set
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Set<NE>
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
        return new Set($elements ?? $this->elements, $this->getComparator());
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