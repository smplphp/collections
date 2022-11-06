<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class FlattenOperation extends BaseOperation
{
    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        $elements = [];

        $this->flattenIterable($value, $elements);

        return $value->copy($elements);
    }

    /**
     * @param iterable<E> $value
     * @param array<E>    $elements
     *
     * @return void
     */
    private function flattenIterable(iterable $value, array &$elements): void
    {
        foreach ($value as $element) {
            if (is_iterable($element)) {
                $this->flattenIterable($element, $elements);
            } else {
                $elements[] = $element;
            }
        }
    }
}