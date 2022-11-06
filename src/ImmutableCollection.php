<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * @template E of mixed
 *
 * @extends \Smpl\Collections\BaseImmutableCollection<int, E>
 *
 * @psalm-immutable
 */
final class ImmutableCollection extends BaseImmutableCollection
{
    /**
     * @template       NI of int
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
    public static function of(mixed ...$elements): static
    {
        return new self(array_values($elements));
    }
}