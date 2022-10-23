<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * Sequence
 *
 * A collection of elements in a list-like structure where each element's order,
 * its sequence, is important.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseSequence<E>
 */
final class Sequence extends BaseSequence
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\Sequence<NE>
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

        return new Sequence($elements, $this->getComparator());
    }
}