<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

final class InvalidArgumentException extends BaseInvalidArgumentException
{
    public static function invalidEnum(string $enumClass): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'Cannot add elements not of type \'%s\'',
            $enumClass
        ));
    }

    public static function invalidEnumCreation(string $enumClass): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'Cannot create a new instance of EnumSet for elements that are not all of the same enum type, expecting %s',
            $enumClass
        ));
    }

    public static function noEnum(): InvalidArgumentException
    {
        return new InvalidArgumentException('Cannot create a new instance of EnumSet without providing the enum class');
    }

    public static function notNullable(): InvalidArgumentException
    {
        return new InvalidArgumentException('Null value passed to a collection that does not accept null values');
    }

    public static function priorityFlagsOrder(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid PrioritisedCollection flags, cannot be ordered both descending as ascending');
    }

    public static function priorityFlagsPlacement(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid PrioritisedCollection flags, cannot have no priority items at the start and end');
    }

    public static function priorityFlagsNull(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid PrioritisedCollection flags, cannot have null items at the start and end');
    }
}