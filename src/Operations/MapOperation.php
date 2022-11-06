<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Support\Collections;
use Smpl\Utils\Contracts\Func;

/**
 * @template E of mixed
 * @template R of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, R>>
 */
final class MapOperation extends BaseOperation
{
    /**
     * @var \Smpl\Utils\Contracts\Func<E, R>
     */
    private Func $mappingFunction;

    /**
     * @param \Smpl\Utils\Contracts\Func<E, R> $mappingFunction
     */
    public function __construct(Func $mappingFunction)
    {
        $this->mappingFunction = $mappingFunction;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, R>
     *
     * @noinspection   PhpRedundantVariableDocTypeInspection
     *
     * @psalm-suppress UnnecessaryVarAnnotation
     */
    public function apply(mixed $value): Collection
    {
        $function = $this->mappingFunction;
        /**  @var list<R> $elements */
        $elements = [];

        /** @var E $element */
        foreach ($value as $index => $element) {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            $elements[$index] = $function->apply($element);
        }

        return Collections::from($value, $elements);
    }
}