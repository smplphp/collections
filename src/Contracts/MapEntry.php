<?php

namespace Smpl\Collections\Contracts;

/**
 * @template K of array-key
 * @template V of mixed
 */
interface MapEntry
{
    /**
     * @return K
     */
    public function getKey(): mixed;

    /**
     * @return V
     */
    public function getValue(): mixed;
}