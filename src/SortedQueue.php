<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\SortsCollection;

/**
 * Sorted Queue
 *
 * A collection designed to hold elements to be processed. It is a basic
 * extension of {@see \Smpl\Collections\Contracts\Collection}.
 *
 * This isn't a FIFO (first-in-first-out) implementation, instead elements
 * added to the queue are ordered using a comparator.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 * @implements \Smpl\Collections\Contracts\Queue<E>
 * @implements \Smpl\Collections\Contracts\SortedCollection<int, E>
 */
final class SortedQueue extends BaseCollection implements Contracts\Queue, Contracts\SortedCollection
{
    use SortsCollection;

    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\SortedQueue<NE>
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
        return new SortedQueue($elements ?? $this->elements, $this->getComparator());
    }

    /**
     * @return E|null
     */
    public function peekFirst(): mixed
    {
        return $this->elements[0] ?? null;
    }

    /**
     * @return E|null
     */
    public function pollFirst(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $element = array_shift($this->elements);

        $this->modifyCount(-1);

        return $element;
    }
}