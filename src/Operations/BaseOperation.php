<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Utils\Contracts\Func;

/**
 * @template T of mixed
 * @template R of mixed
 * @implements \Smpl\Utils\Contracts\Func<T, R>
 */
abstract class BaseOperation implements Func
{
    /**
     * @param T $value
     *
     * @return R
     */
    public function __invoke(mixed $value): mixed
    {
        return $this->apply($value);
    }
}