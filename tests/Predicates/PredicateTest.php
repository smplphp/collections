<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Predicates;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Collection;
use Smpl\Collections\Predicates\CallablePredicate;
use Smpl\Collections\Predicates\ContainsPredicate;

/**
 * @group predicates
 */
class PredicateTest extends TestCase
{
    /**
     * @var \Smpl\Collections\Predicates\CallablePredicate
     */
    private CallablePredicate $predicate;

    protected function setUp(): void
    {
        $this->predicate = new CallablePredicate(fn($v) => ($v % 2) === 0);
    }

    /**
     * @test
     */
    public function callablePredicatePerformsAsExpected(): void
    {
        self::assertTrue($this->predicate->test(2));
        self::assertTrue($this->predicate->test(4));
        self::assertTrue($this->predicate->test(6));
        self::assertTrue($this->predicate->test(8));

        self::assertFalse($this->predicate->test(1));
        self::assertFalse($this->predicate->test(3));
        self::assertFalse($this->predicate->test(5));
        self::assertFalse($this->predicate->test(7));
    }

    /**
     * @test
     */
    public function predicatesAreNegatable(): void
    {
        $predicate = $this->predicate->negate();

        self::assertFalse($predicate->test(2));
        self::assertFalse($predicate->test(4));
        self::assertFalse($predicate->test(6));
        self::assertFalse($predicate->test(8));

        self::assertTrue($predicate->test(1));
        self::assertTrue($predicate->test(3));
        self::assertTrue($predicate->test(5));
        self::assertTrue($predicate->test(7));
    }

    /**
     * @test
     */
    public function predicatesAreAndable(): void
    {
        $predicate = $this->predicate->and(fn($v) => ($v % 3) === 0);

        self::assertFalse($predicate->test(2));
        self::assertFalse($predicate->test(4));
        self::assertFalse($predicate->test(8));
        self::assertFalse($predicate->test(10));

        self::assertTrue($predicate->test(6));
        self::assertTrue($predicate->test(12));
        self::assertTrue($predicate->test(18));
        self::assertTrue($predicate->test(24));
    }

    /**
     * @test
     */
    public function predicatesAreOrable(): void
    {
        $predicate = $this->predicate->or(fn($v) => ($v % 3) === 0);

        self::assertTrue($predicate->test(2));
        self::assertTrue($predicate->test(3));
        self::assertTrue($predicate->test(4));
        self::assertTrue($predicate->test(6));

        self::assertFalse($predicate->test(5));
        self::assertFalse($predicate->test(7));
        self::assertFalse($predicate->test(11));
        self::assertFalse($predicate->test(13));
    }

    /**
     * @test
     */
    public function containsPredicatePerformsAsExpected(): void
    {
        $collection1 = new Collection([
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
        ]);
        $collection2 = new Collection([
            1,
            3,
            5,
            7,
            9,
        ]);

        $predicate   = new ContainsPredicate($collection2);
        $collection1->removeIf($predicate);

        self::assertCount(4, $collection1);
        self::assertFalse($collection1->contains(1));
        self::assertTrue($collection1->contains(2));
        self::assertFalse($collection1->contains(3));
        self::assertTrue($collection1->contains(4));
        self::assertFalse($collection1->contains(5));
        self::assertTrue($collection1->contains(6));
        self::assertFalse($collection1->contains(7));
        self::assertTrue($collection1->contains(8));
        self::assertFalse($collection1->contains(9));
    }
}