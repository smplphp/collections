<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Iterator;
use Smpl\Collections\Iterators\SimpleIterator;

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
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Deque<NE>
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
        return new Deque($elements ?? $this->elements, $this->getComparator());
    }
}