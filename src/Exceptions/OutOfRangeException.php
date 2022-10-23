<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use OutOfRangeException as BaseOutOfRangeException;

final class OutOfRangeException extends BaseOutOfRangeException
{
    public static function index(int $index, int $min, int $max): OutOfRangeException
    {
        return new self(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            $index, $min, $max
        ));
    }

    public static function subsetLength(int $index, int $length, int $min, int $max): OutOfRangeException
    {
        return new self(sprintf(
            'The subset index %s and length %s, would result in indexes outside the range of %s <> %s',
            $index, $length, $min, $max
        ));
    }
}