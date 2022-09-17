<?php
declare(strict_types=1);

namespace Smpl\Collections\Exceptions;

use Exception;

class PredicateException extends Exception
{
    public static function notEnough(int $count, int $requiredCount, string $operation): NotEnoughPredicatesException
    {
        return new NotEnoughPredicatesException(sprintf(
            'Only %s predicates were provided, but %s or more are required for %s',
            $count, $requiredCount, $operation
        ));
    }
}