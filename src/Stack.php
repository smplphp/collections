<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
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