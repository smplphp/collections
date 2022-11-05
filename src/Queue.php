<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * Queue
 *
 * A collection designed to hold elements to be processed. It is a basic
 * extension of {@see \Smpl\Collections\Contracts\Collection}.
 *
 * This is a FIFO (first-in-first-out) implementation.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Queue<E>
 */
final class Queue extends BaseCollection implements Contracts\Queue
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Queue<NE>
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
        return new Queue($elements ?? $this->elements, $this->getComparator());
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