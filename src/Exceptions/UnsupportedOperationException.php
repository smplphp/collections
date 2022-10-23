<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use Exception;

final class UnsupportedOperationException extends Exception
{
    /**
     * @param class-string $class
     * @param string       $method
     *
     * @return static
     */
    public static function mutable(string $class, string $method): UnsupportedOperationException
    {
        return new self(message: sprintf(
            '%s is immutable, and does not support the mutable method %s',
            $class, $method
        ));
    }
}