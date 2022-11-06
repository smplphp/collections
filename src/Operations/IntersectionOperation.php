<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Concerns\SuppliesElementsForOperation;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class IntersectionOperation extends BaseOperation
{
    /** @use \Smpl\Collections\Concerns\SuppliesElementsForOperation<E> */
    use SuppliesElementsForOperation;

    /**
     * @param \Smpl\Utils\Contracts\Supplier<\Smpl\Collections\Contracts\Collection<array-key, E>>|iterable<E> $intersectWith
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(Supplier|iterable $intersectWith)
    {
        $this->setElementSupplier($intersectWith);
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        $value->retainAll($this->getElements());

        return $value;
    }
}