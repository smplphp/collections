<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Helpers\ComparisonHelper;

/**
 * Get Sign Function
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
 * @uses \Smpl\Collections\Helpers\ComparisonHelper::signum()
 *
 * @psalm-pure
 * @phpstan-pure
 */
function get_sign(int|float $number): int
{
    return ComparisonHelper::signum($number);
}

/**
 * Does Sign Match Function
 *
 * Check if the sign of the provided number matches the provided sign.
 *
 * @param int|float $number
 * @param int<-1,1> $signum
 *
 * @return bool
 *
 * @psalm-pure
 * @phpstan-pure
 */
function does_sign_match(int|float $number, int $signum): bool
{
    return get_sign($number) === $signum;
}