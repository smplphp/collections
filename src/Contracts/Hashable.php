<?php

namespace Smpl\Collections\Contracts;

/**
 * @psalm-immutable
 */
interface Hashable
{
    /**
     * @return string
     */
    public function getHashCode(): string;
}