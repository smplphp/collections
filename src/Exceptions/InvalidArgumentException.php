<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;
use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Hashable;
use Smpl\Utils\Contracts\Supplier;

final class InvalidArgumentException extends BaseInvalidArgumentException
{
    public static function collectorNoCollection(): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'Collectors can only be created for classes that implement %s',
            Collection::class
        ));
    }

    public static function invalidElementSupplier(string $operationClass): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'The %s operation requires an iterable or a %s that supplies an iterable',
            $operationClass,
            Supplier::class
        ));
    }

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

    public static function noKey(): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'Map values must be added with a key, or be an object implementing %s',
            Hashable::class
        ));
    }

    public static function noReplacementFromRetriever(string|int $key): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'Value retriever did not yield a value to replace key \'%s\'',
            $key
        ));
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