<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

final class InvalidArgumentException extends BaseInvalidArgumentException
{
    public static function notNullable(): InvalidArgumentException
    {
        return new InvalidArgumentException('Null value passed to a collection that does not accept null values');
    }

    public static function priorityFlagsOrder(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid PriorityQueue flags, cannot be ordered both descending as ascending');
    }

    public static function priorityFlagsPlacement(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid PriorityQueue flags, cannot have no priority items at the start and end');
    }

    public static function priorityFlagsNull(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid PriorityQueue flags, cannot have null items at the start and end');
    }
}