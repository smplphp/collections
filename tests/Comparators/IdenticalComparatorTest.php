<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Comparators;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Comparators\DefaultComparator;
use Smpl\Collections\Comparators\IdenticalComparator;
use Smpl\Collections\Helpers\ComparisonHelper;

/**
 * @group identical-comparator
 */
class IdenticalComparatorTest extends TestCase
{
    /**
     * @var \Smpl\Collections\Comparators\IdenticalComparator
     */
    private IdenticalComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new IdenticalComparator();
    }

    public function valueComparisonProvider(): array
    {
        $stdClass            = new \stdClass();
        $stdClass2           = clone $stdClass;
        $stdClass2->property = false;
        $stdClass3           = clone $stdClass2;
        $stdClass3->property = true;
        $stdClass4           = clone $stdClass;

        return [
            '1 === 1'               => [1, 1, ComparisonHelper::EQUAL_TO],
            '1 < 2'                 => [1, 2, ComparisonHelper::LESS_THAN],
            '1 < -1'                => [1, -1, ComparisonHelper::LESS_THAN],
            '\'1\' < 1'           => ['1', 1, ComparisonHelper::LESS_THAN],
            '\'1\' < 2'             => ['1', 2, ComparisonHelper::LESS_THAN],
            '\'1\' < -1'            => ['1', -1, ComparisonHelper::LESS_THAN],
            '\'a\' < 0'             => ['a', 0, ComparisonHelper::LESS_THAN],
            '\'a\' < 1'             => ['a', 1, ComparisonHelper::LESS_THAN],
            '\'a\' < 2'             => ['a', 2, ComparisonHelper::LESS_THAN],
            '0 < \'a\''             => [0, 'a', ComparisonHelper::LESS_THAN],
            '1 < \'a\''             => [1, 'a', ComparisonHelper::LESS_THAN],
            '2 < \'a\''             => [2, 'a', ComparisonHelper::LESS_THAN],
            '\'a\' === \'a\''       => ['a', 'a', ComparisonHelper::EQUAL_TO],
            '\'a\' < \'b\''         => ['a', 'b', ComparisonHelper::LESS_THAN],
            '\'b\' < \'a\''         => ['b', 'a', ComparisonHelper::LESS_THAN],
            '\'a\' < \'A\''         => ['a', 'A', ComparisonHelper::LESS_THAN],
            '\'A\' < \'a\''         => ['A', 'a', ComparisonHelper::LESS_THAN],
            '\'A\' < \'B\''         => ['A', 'a', ComparisonHelper::LESS_THAN],
            '1 < true'            => [1, true, ComparisonHelper::LESS_THAN],
            '1 < false'             => [1, false, ComparisonHelper::LESS_THAN],
            '0 < false'           => [0, false, ComparisonHelper::LESS_THAN],
            '0 < true'              => [0, true, ComparisonHelper::LESS_THAN],
            'stdClass === stdClass' => [$stdClass, $stdClass, ComparisonHelper::EQUAL_TO],
            'stdClass < stdClass2'  => [$stdClass, $stdClass2, ComparisonHelper::LESS_THAN],
            'stdClass2 < stdClass3' => [$stdClass2, $stdClass3, ComparisonHelper::LESS_THAN],
            'stdClass3 < stdClass4' => [$stdClass3, $stdClass4, ComparisonHelper::LESS_THAN],
        ];
    }

    /**
     * @test
     * @dataProvider valueComparisonProvider
     */
    public function comparesValuesProperly(mixed $a, mixed $b, int $result): void
    {
        self::assertSame($result, $this->comparator->compare($a, $b));
    }

    /**
     * @test
     * @dataProvider valueComparisonProvider
     */
    public function comparisonDoesNotChangeWhenReversed(mixed $a, mixed $b, int $result): void
    {
        self::assertSame($result, $this->comparator->compare($b, $a));
    }

    /**
     * @test
     * @dataProvider valueComparisonProvider
     */
    public function isInvokableAndFunctionsIdenticallyToCompareCall(mixed $a, mixed $b, int $result): void
    {
        $comparator = $this->comparator;

        self::assertSame($comparator($a, $b), $comparator->compare($a, $b));
    }
}