<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * Deque
 *
 * A deque, which stands for double-ended-queue and is pronounced deck. A Deque
 * functions as both {@see \Smpl\Collections\Contracts\Queue} and
 * {@see \Smpl\Collections\Contracts\Stack}.
 *
 * For the purpose of the following methods, this collection will behave like a
 * {@see \Smpl\Collections\Contracts\Queue}.
 *
 *   - {@see \Smpl\Collections\Contracts\Deque::peek()}
 *   - {@see \Smpl\Collections\Contracts\Deque::poll()}
 *
 * These methods are provided by this contract in an attempt
 * to provide some consistency. Each of these methods, either through the
 * Queue, Stack or this contract will have a variation suffixed with "last"
 * or "first" for more controlled handling.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseDeque<E>
 */
final class Deque extends BaseDeque
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
}