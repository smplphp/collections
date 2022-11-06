<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\SortsCollection;
use Smpl\Collections\Contracts\SortedCollection as SortedCollectionContract;

/**
 * @template E of mixed
 *
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\SortedCollection<int, E>
 */
final class SortedCollection extends BaseCollection implements SortedCollectionContract
{
    /** @use \Smpl\Collections\Concerns\SortsCollection<int, E> */
    use SortsCollection;

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