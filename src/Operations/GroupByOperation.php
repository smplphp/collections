<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Collection;
use Smpl\Collections\Contracts\Map as MapContract;
use Smpl\Collections\Map;
use Smpl\Utils\Contracts\BiFunc;
use Smpl\Utils\Contracts\Func;

/**
 * @template E of mixed
 * @template K of array-key
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Map<K, \Smpl\Collections\Contracts\Collection<int, E>>>
 */
final class GroupByOperation extends BaseOperation
{
    /**
     * @var \Smpl\Utils\Contracts\BiFunc<E, K, array-key>|\Smpl\Utils\Contracts\Func<E, array-key>
     */
    private BiFunc|Func $groupingFunction;

    /**
     * @param \Smpl\Utils\Contracts\BiFunc<E, K, array-key>|\Smpl\Utils\Contracts\Func<E, array-key> $groupingFunction
     */
    public function __construct(Func|BiFunc $groupingFunction)
    {
        $this->groupingFunction = $groupingFunction;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Map<K, \Smpl\Collections\Contracts\Collection<int, E>>
     */
    public function apply(mixed $value): MapContract
    {
        /**
         * @var \Smpl\Collections\Contracts\Map<K, \Smpl\Collections\Contracts\Collection<int, E>> $map
         */
        $map              = new Map();
        $groupingFunction = $this->groupingFunction;

        foreach ($value as $key => $element) {
            /**
             * @psalm-suppress TooManyArguments
             */
            $group = $groupingFunction($element, $key);

            if (! $map->has($group)) {
                $map->add(new Collection([$element]), $group);
            } else {
                $map->get($group)?->add($element);
            }
        }

        return $map;
    }
}