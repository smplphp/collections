<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * Stack
 *
 * A collection designed to hold elements to be processed. It is a basic
 * extension of {@see \Smpl\Collections\Contracts\Collection}.
 *
 * This is a LIFO (last-in-first-out) implementation.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Stack<E>
 */
final class Stack extends BaseCollection implements Contracts\Stack
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Stack<NE>
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
        return new Stack($elements ?? $this->elements, $this->getComparator());
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