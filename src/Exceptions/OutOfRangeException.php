<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use OutOfRangeException as BaseOutOfRangeException;
use Smpl\Utils\Support\Range;

final class OutOfRangeException extends BaseOutOfRangeException
{
    /**
     * @template T of int|string|float
     *
     * @param int|string|float             $value
     * @param \Smpl\Utils\Support\Range<T> $range
     * @param bool                         $exclusive
     *
     * @return \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public static function fromRange(int|string|float $value, Range $range, bool $exclusive = true): OutOfRangeException
    {
        $end = $range->end();

        if (is_int($end) && $exclusive) {
            --$end;
        }

        return self::index($value, $range->start(), $end);
    }

    public static function index(int|string|float $index, int|string|float $min, int|string|float $max): OutOfRangeException
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