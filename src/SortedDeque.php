<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\SortsCollection;

/**
 * Sorted Deque
 *
 * A deque, which stands for double-ended-queue and is pronounced deck. A Deque
 * functions as both {@see \Smpl\Collections\Contracts\Queue} and
 * {@see \Smpl\Collections\Contracts\Stack}.
 *
 * Elements added to this deque are ordered using a comparator.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseDeque<E>
 * @implements \Smpl\Collections\Contracts\SortedCollection<int, E>
 */
final class SortedDeque extends BaseDeque implements Contracts\SortedCollection
{
    use SortsCollection;

    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\SortedDeque<NE>
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
        return new SortedDeque($elements ?? $this->elements, $this->getComparator());
    }
}