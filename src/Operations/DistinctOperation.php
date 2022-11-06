<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Helpers\IterableHelper;
use Smpl\Utils\Contracts\ComparesValues;

/**
 * @template E of mixed
 *
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class DistinctOperation extends BaseOperation
{
    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        $elements   = [];
        $comparator = null;

        if ($value instanceof ComparesValues) {
            $comparator = $value->getComparator();
        }

        foreach ($value as $element) {
            if (IterableHelper::contains($elements, $element, $comparator)) {
                $elements[] = $element;
            }
        }

        return $value->copy($elements);
    }
}