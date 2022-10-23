<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\SortsCollection;
use Smpl\Collections\Contracts\SortedCollection as SortedCollectionContract;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 * @implements \Smpl\Collections\Contracts\SortedCollection<int, E>
 */
final class SortedCollection extends BaseCollection implements SortedCollectionContract
{
    use SortsCollection;

    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\SortedCollection<NE>
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
        $elements ??= $this->elements;

        return new SortedCollection($elements, $this->getComparator());
    }
}