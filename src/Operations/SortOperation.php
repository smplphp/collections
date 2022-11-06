<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Support\Collections;
use Smpl\Utils\Contracts\Comparator;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
class SortOperation extends BaseOperation
{
    /**
     * @var \Smpl\Utils\Contracts\Comparator<E>
     */
    private Comparator $comparator;

    /**
     * @param \Smpl\Utils\Contracts\Comparator<E> $comparator
     */
    public function __construct(Comparator $comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): Collection
    {
        $elements = $value->toArray();

        usort($elements, $this->comparator);

        return Collections::from($value, $elements);
    }
}