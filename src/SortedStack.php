<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\SortsCollection;

/**
 * Sorted Stack
 *
 * A collection designed to hold elements to be processed. It is a basic
 * extension of {@see \Smpl\Collections\Contracts\Collection}.
 *
 * This isn't a LIFO (last-in-first-out) implementation, instead elements
 * added to the queue are ordered using a comparator.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 * @implements \Smpl\Collections\Contracts\Stack<E>
 * @implements \Smpl\Collections\Contracts\SortedCollection<int, E>
 */
final class SortedStack extends BaseCollection implements Contracts\Stack, Contracts\SortedCollection
{
    use SortsCollection;

    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\SortedStack<NE>
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
        return new SortedStack($elements ?? $this->elements, $this->getComparator());
    }

    /**
     * @return E|null
     */
    public function peekLast(): mixed
    {
        return $this->elements[$this->getMaxIndex()] ?? null;
    }

    /**
     * @return E|null
     */
    public function pollLast(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $element = array_pop($this->elements);

        $this->modifyCount(-1);

        return $element;
    }
}