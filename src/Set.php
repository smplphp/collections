<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
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
     * @template       NI of array-key
     * @template       NE of mixed
     *
     * @param iterable<NI, NE>|null $elements
     *
     * @return static
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @noinspection   PhpDocSignatureInspection
     */
    public function copy(iterable $elements = null): static
    {
        /** @psalm-suppress InvalidArgument */
        return new self($elements ?? $this->getElements(), $this->getComparator());
    }

    /**
     * @template NE of mixed
     *
     * @param NE ...$elements
     *
     * @return static<NE>
     */
    public static function of(...$elements): static
    {
        return new self($elements);
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