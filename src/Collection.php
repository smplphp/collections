<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 */
final class Collection extends BaseCollection
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Collection<NE>
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

        return new Collection($elements, $this->getComparator());
    }
}