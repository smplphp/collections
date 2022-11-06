<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class DropOperation extends BaseOperation
{
    /**
     * @var int<0, max>
     */
    private int $drop;

    /**
     * @param int<0, max> $drop
     */
    public function __construct(int $drop)
    {
        $this->drop = $drop;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        return (new SliceOperation($this->drop))->apply($value);
    }
}