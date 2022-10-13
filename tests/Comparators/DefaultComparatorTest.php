<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Comparators;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Comparators\DefaultComparator;
use Smpl\Collections\Helpers\ComparisonHelper;

/**
 * @group default-comparator
 */
class DefaultComparatorTest extends TestCase
{
    /**
     * @var \Smpl\Collections\Comparators\DefaultComparator
     */
    private DefaultComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new DefaultComparator();
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
            '1 > -1'                => [1, -1, ComparisonHelper::MORE_THAN],
            '\'1\' === 1'           => ['1', 1, ComparisonHelper::EQUAL_TO],
            '\'1\' < 2'             => ['1', 2, ComparisonHelper::LESS_THAN],
            '\'1\' > -1'            => ['1', -1, ComparisonHelper::MORE_THAN],
            '\'a\' > 0'             => ['a', 0, ComparisonHelper::MORE_THAN],
            '\'a\' > 1'             => ['a', 1, ComparisonHelper::MORE_THAN],
            '\'a\' > 2'             => ['a', 2, ComparisonHelper::MORE_THAN],
            '0 < \'a\''             => [0, 'a', ComparisonHelper::LESS_THAN],
            '1 < \'a\''             => [1, 'a', ComparisonHelper::LESS_THAN],
            '2 < \'a\''             => [2, 'a', ComparisonHelper::LESS_THAN],
            '\'a\' === \'a\''       => ['a', 'a', ComparisonHelper::EQUAL_TO],
            '\'a\' < \'b\''         => ['a', 'b', ComparisonHelper::LESS_THAN],
            '\'b\' > \'a\''         => ['b', 'a', ComparisonHelper::MORE_THAN],
            '\'a\' > \'A\''         => ['a', 'A', ComparisonHelper::MORE_THAN],
            '\'A\' < \'a\''         => ['A', 'a', ComparisonHelper::LESS_THAN],
            '\'A\' < \'B\''         => ['A', 'a', ComparisonHelper::LESS_THAN],
            '1 === true'            => [1, true, ComparisonHelper::EQUAL_TO],
            '1 > false'             => [1, false, ComparisonHelper::MORE_THAN],
            '0 === false'           => [0, false, ComparisonHelper::EQUAL_TO],
            '0 < true'              => [0, true, ComparisonHelper::LESS_THAN],
            'stdClass === stdClass' => [$stdClass, $stdClass, ComparisonHelper::EQUAL_TO],
            'stdClass < stdClass2'  => [$stdClass, $stdClass2, ComparisonHelper::LESS_THAN],
            'stdClass2 < stdClass3' => [$stdClass2, $stdClass3, ComparisonHelper::LESS_THAN],
            'stdClass3 > stdClass4' => [$stdClass3, $stdClass4, ComparisonHelper::MORE_THAN],
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
    public function comparisonFlipsWhenReversed(mixed $a, mixed $b, int $result): void
    {
        self::assertSame($result, ComparisonHelper::flip($this->comparator->compare($b, $a)));
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