<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Utils\Contracts\BiFunc;

/**
 * @template E of mixed
 * @template R of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, R>
 */
final class ReduceOperation extends BaseOperation
{
    /**
     * @var mixed|null
     */
    private mixed $initialValue;

    /**
     * @var \Smpl\Utils\Contracts\BiFunc<E, R|null, R>
     */
    private BiFunc $reductionFunc;

    /**
     * @param \Smpl\Utils\Contracts\BiFunc<E, R|null, R> $reductionFunc
     * @param R|null                                     $initialValue
     */
    public function __construct(BiFunc $reductionFunc, mixed $initialValue = null)
    {
        $this->reductionFunc = $reductionFunc;
        $this->initialValue  = $initialValue;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return R|null
     */
    public function apply(mixed $value): mixed
    {
        /** @var R|null $reduction */
        $reduction = $this->initialValue;

        foreach ($value as $element) {
            $reduction = $this->reductionFunc->apply($element, $reduction);
        }

        return $reduction;
    }
}