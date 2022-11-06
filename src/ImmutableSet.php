<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Utils\Contracts\Comparator;

/**
 * Immutable Set
 *
 * An immutable base collection, exactly like {@see \Smpl\Collections\Collection},
 * except that it does not allow duplicates.
 *
 * @template E of mixed
 *
 * @extends \Smpl\Collections\BaseImmutableCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Set<E>
 *
 * @psalm-immutable
 */
final class ImmutableSet extends BaseImmutableCollection implements Contracts\Set
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
        return new self($elements ?? $this->getElements(), $this->getComparator());
    }

    /**
     * @param iterable<E>|null                         $elements
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(iterable $elements = null, ?Comparator $comparator = null)
    {
        $this->comparator = $comparator;

        if ($elements !== null) {
            foreach ($elements as $element) {
                if (! $this->contains($element)) {
                    $this->elements[] = $element;
                    $this->count++;
                }
            }
        }
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