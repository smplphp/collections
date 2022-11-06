<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

/**
 * @template K of array-key
 * @template V of mixed
 * @extends \Smpl\Collections\BaseMap<K, V>
 */
final class Map extends BaseMap
{
    /**
     * @template       NK of array-key
     * @template       NE of mixed
     *
     * @param iterable<NK, NE>|null $elements
     *
     * @return static
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
     * @return static
     */
    public static function of(mixed ...$elements): static
    {
        return new self($elements);
    }
}