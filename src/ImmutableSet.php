<?php
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
 * @extends \Smpl\Collections\BaseImmutableCollection<E>
 * @implements \Smpl\Collections\Contracts\Set<E>
 * @psalm-immutable
 */
final class ImmutableSet extends BaseImmutableCollection implements Contracts\Set
{
    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\ImmutableSet<NE>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     * @psalm-suppress ImpureMethodCall
     *
     * @noinspection   PhpDocSignatureInspection
     * @noinspection   PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null): static
    {
        return new ImmutableSet($elements ?? $this->elements, $this->getComparator());
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
}