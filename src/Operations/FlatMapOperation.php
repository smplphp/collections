<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Utils\Contracts\Func;

/**
 * @template E of mixed
 * @template R of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class FlatMapOperation extends BaseOperation
{
    /**
     * @var \Smpl\Collections\Operations\MapOperation<E, R>
     */
    private MapOperation $mapOperation;

    /**
     * @var \Smpl\Collections\Operations\FlattenOperation<R>
     */
    private FlattenOperation $flattenOperation;

    /**
     * @param \Smpl\Utils\Contracts\Func<E, R> $mappingFunction
     */
    public function __construct(Func $mappingFunction)
    {
        $this->mapOperation     = new MapOperation($mappingFunction);
        $this->flattenOperation = new FlattenOperation();
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, R>
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function apply(mixed $value): mixed
    {
        return $this->flattenOperation->apply($this->mapOperation->apply($value));
    }
}