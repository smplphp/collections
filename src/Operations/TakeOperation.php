<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class TakeOperation extends BaseOperation
{
    /**
     * @var int<0, max>
     */
    private int $take;

    /**
     * @param int<0, max> $take
     */
    public function __construct(int $take)
    {
        $this->take = $take;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        return (new SliceOperation(0, $this->take))->apply($value);
    }
}