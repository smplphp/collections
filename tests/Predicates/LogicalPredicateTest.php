<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Predicates;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Exceptions\NotEnoughPredicatesException;
use Smpl\Collections\Predicates\CallablePredicate;
use Smpl\Collections\Predicates\Logical\AndPredicate;
use Smpl\Collections\Predicates\Logical\NotPredicate;
use Smpl\Collections\Predicates\Logical\OrPredicate;

/**
 * @group predicates
 * @group logical
 */
class LogicalPredicateTest extends TestCase
{
    /**
     * @test
     */
    public function logicalAndPredicatePerformsAsExpected(): void
    {
        $predicate = new AndPredicate(
            new CallablePredicate(fn($v) => ($v % 2) === 0),
            new CallablePredicate(fn($v) => ($v % 3) === 0),
        );

        self::assertTrue($predicate->test(6));
        self::assertTrue($predicate->test(12));
        self::assertTrue($predicate->test(18));
        self::assertTrue($predicate->test(24));

        self::assertFalse($predicate->test(4));
        self::assertFalse($predicate->test(3));
        self::assertFalse($predicate->test(8));
        self::assertFalse($predicate->test(9));
    }

    /**
     * @test
     */
    public function logicalAndPredicateThrowsExceptionIfLessThanTwoPredicatesAreProvided(): void
    {
        $this->expectException(NotEnoughPredicatesException::class);
        $this->expectExceptionMessage('Only 1 predicates were provided, but 2 or more are required for ' . AndPredicate::class);

        new AndPredicate(
            new CallablePredicate(fn($v) => ($v % 2) === 0)
        );
    }

    /**
     * @test
     */
    public function logicalOrPredicatePerformsAsExpected(): void
    {
        $predicate = new OrPredicate(
            new CallablePredicate(fn($v) => ($v % 2) === 0),
            new CallablePredicate(fn($v) => ($v % 3) === 0),
        );

        self::assertTrue($predicate->test(2));
        self::assertTrue($predicate->test(3));
        self::assertTrue($predicate->test(6));
        self::assertTrue($predicate->test(8));
        self::assertTrue($predicate->test(12));

        self::assertFalse($predicate->test(1));
        self::assertFalse($predicate->test(5));
        self::assertFalse($predicate->test(7));
        self::assertFalse($predicate->test(11));
    }

    /**
     * @test
     */
    public function logicalOrPredicateThrowsExceptionIfLessThanTwoPredicatesAreProvided(): void
    {
        $this->expectException(NotEnoughPredicatesException::class);
        $this->expectExceptionMessage('Only 1 predicates were provided, but 2 or more are required for ' . OrPredicate::class);

        new OrPredicate(
            new CallablePredicate(fn($v) => ($v % 2) === 0)
        );
    }

    /**
     * @test
     */
    public function logicalNotPredicatePerformsAsExpected(): void
    {
        $predicate = new NotPredicate(
            new CallablePredicate(fn($v) => ($v % 2) === 0)
        );

        self::assertTrue($predicate->test(1));
        self::assertTrue($predicate->test(3));
        self::assertTrue($predicate->test(5));
        self::assertTrue($predicate->test(7));

        self::assertFalse($predicate->test(2));
        self::assertFalse($predicate->test(4));
        self::assertFalse($predicate->test(6));
        self::assertFalse($predicate->test(8));
    }
}