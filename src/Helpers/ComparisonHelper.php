<?php
declare(strict_types=1);

namespace Smpl\Collections\Helpers;

/**
 * Comparison Helper
 *
 * A class to help with comparison.
 */
final class ComparisonHelper
{
    /**
     * The recommended value to represent A being less than B.
     */
    public final const LESS_THAN = -1;

    /**
     * The recommended value to represent A being equal to B.
     */
    public final const EQUAL_TO = 0;

    /**
     * The recommended value to represent A being more than B.
     */
    public final const MORE_THAN = 1;

    /**
     * Signum Function
     *
     * Simple implementation of the sign or signum function that extracts the
     * sign of a real number. Negative numbers are -1, position numbers are 1, and 0
     * are 0.
     *
     * @param int|float $number
     *
     * @return int<-1, 1>
     *
     * @link https://en.wikipedia.org/wiki/Sign_function
     *
     * @psalm-pure
     * @phpstan-pure
     */
    public static function signum(int|float $number): int
    {
        if ($number < 0) {
            return self::LESS_THAN;
        }

        if ($number === 0) {
            return self::EQUAL_TO;
        }

        return self::MORE_THAN;
    }
}