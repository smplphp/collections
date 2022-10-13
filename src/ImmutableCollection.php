<?php
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\BaseImmutableCollection<E>
 * @psalm-immutable
 */
final class ImmutableCollection extends BaseImmutableCollection
{
    /**
     * @template     NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\ImmutableCollection<NE>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     *
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null): static
    {
        $elements ??= $this->elements;

        return new ImmutableCollection($elements);
    }
}