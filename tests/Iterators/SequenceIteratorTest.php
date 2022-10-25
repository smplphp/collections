<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Iterators;

use PHPUnit\Framework\TestCase;
use Smpl\Collections\Exceptions\OutOfRangeException;
use Smpl\Collections\Iterators\SequenceIterator;
use Smpl\Collections\Sequence;

/**
 * @group iterator
 * @group sequence
 */
class SequenceIteratorTest extends TestCase
{
    public function elementIteratorProvider(): array
    {
        $iterator = new SequenceIterator(new Sequence([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]));

        return [
            'Index 0'  => [$iterator, 0, 0, true],
            'Index 1'  => [$iterator, 1, 1, true],
            'Index 2'  => [$iterator, 2, 2, true],
            'Index 3'  => [$iterator, 3, 3, true],
            'Index 4'  => [$iterator, 4, 4, true],
            'Index 5'  => [$iterator, 5, 5, true],
            'Index 6'  => [$iterator, 6, 6, true],
            'Index 7'  => [$iterator, 7, 7, true],
            'Index 8'  => [$iterator, 8, 8, true],
            'Index 9'  => [$iterator, 9, 9, true],
            'Index 10' => [$iterator, null, false, false],
            'Index 11' => [$iterator, null, false, false],
        ];
    }

    /**
     * @test
     * @dataProvider elementIteratorProvider
     */
    public function iteratesForAllElements(SequenceIterator $iterator, ?int $key, int|bool $current, bool $valid): void
    {
        self::assertSame($key, $iterator->key());
        self::assertSame($valid, $iterator->valid());

        if ($valid === false) {
            $this->expectException(OutOfRangeException::class);
            $iterator->current();
        } else {
            self::assertSame($current, $iterator->current());
        }

        $iterator->next();
    }

    /**
     * @test
     */
    public function isRewindable(): void
    {
        $iterator = new SequenceIterator(new Sequence([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $data     = [
            0  => [0, 0, true],
            1  => [1, 1, true],
            2  => [2, 2, true],
            3  => [3, 3, true],
            4  => [4, 4, true],
            5  => [5, 5, true],
            6  => [6, 6, true],
            7  => [7, 7, true],
            8  => [8, 8, true],
            9  => [9, 9, true],
            10 => [null, false, false],
            11 => [null, false, false],
        ];

        foreach ($data as [$key, $current, $valid]) {
            self::assertSame($key, $iterator->key());
            if ($current === false) {
                $this->expectException(OutOfRangeException::class);
                $iterator->current();
            } else {
                self::assertSame($current, $iterator->current());
            }

            self::assertSame($valid, $iterator->valid());

            $iterator->next();
        }

        $iterator->rewind();

        self::assertSame(0, $iterator->key());
        self::assertSame(0, $iterator->current());
        self::assertTrue($iterator->valid());
    }

    public function seekingProvider(): array
    {
        $iterator = new SequenceIterator(new Sequence([1, 4, 7, 234, 9, 5345, 6, 1, 9]));

        return [
            'Seek to 3'   => [$iterator, 3, 234],
            'Seek to 9'   => [$iterator, 8, 9],
            'Seek to -1'  => [$iterator, -1, null],
            'Seek to 100' => [$iterator, 100, null],
        ];
    }

    /**
     * @test
     * @dataProvider seekingProvider
     */
    public function canSeekToIndex(SequenceIterator $iterator, int $index, ?int $value): void
    {
        if ($value === null) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $iterator->count() - 1
            ));
        }

        $iterator->seek($index);
        self::assertEquals($value, $iterator->current());
    }

    public function putProvider(): array
    {
        $iterator = new SequenceIterator(new Sequence([1, 4, 7, 234, 9, 5345, 6, 1, 9]));

        return [
            'Put 14 at 3'    => [$iterator, 3, 14, 234],
            'Put -7000 at 8' => [$iterator, 8, -7000, 1],
            'Put 9001 at 1'  => [$iterator, -1, 9001, null],
            'Put 14 at 100'  => [$iterator, 100, 14, null],
        ];
    }

    /**
     * @test
     * @dataProvider putProvider
     */
    public function canPutAtCurrentIndex(SequenceIterator $iterator, int $index, int $element, ?int $displacedElement): void
    {
        if ($displacedElement === null) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $iterator->count() - 1
            ));
        }

        $iterator->seek($index);
        $iterator->put($element);

        self::assertEquals($element, $iterator->current());
        $iterator->next();
        self::assertEquals($displacedElement, $iterator->current());
    }

    public function putAllProvider(): array
    {
        return [
            'Put [14, 68, 99] at 3'               => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 3, [14, 68, 99], 234],
            'Put [444, 444, 12] at 9'             => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 9, [444, 444, 12], null],
            'Put [-7000, -1000, -2000] at 8'      => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 8, [-7000, -1000, -2000], 9],
            'Put [32, 64] at 1'                   => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], -1, [32, 64], null],
            'Put [9000, 9001, 9002, 9003] at 100' => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 100, [9000, 9001, 9002, 9003], null],
        ];
    }

    /**
     * @test
     * @dataProvider putAllProvider
     */
    public function canPutAllAtCurrentIndex(array $sequenceElements, int $index, array $elements, ?int $displacedElement): void
    {
        $iterator = new SequenceIterator(new Sequence($sequenceElements));

        if ($displacedElement === null) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $iterator->count() - 1
            ));
        }

        $iterator->seek($index);
        $iterator->putAll($elements);

        foreach ($elements as $element) {
            self::assertEquals($element, $iterator->current());
            $iterator->next();
        }

        self::assertEquals($displacedElement, $iterator->current());
    }

    public function setProvider(): array
    {
        $iterator = new SequenceIterator(new Sequence([1, 4, 7, 234, 9, 5345, 6, 1, 9]));

        return [
            'Put 14 at 3'    => [$iterator, 3, 14, 9, false],
            'Put -7000 at 8' => [$iterator, 8, -7000, null, false],
            'Put 9001 at 1'  => [$iterator, -1, 9001, null, true],
            'Put 14 at 100'  => [$iterator, 100, 14, null, true],
        ];
    }

    /**
     * @test
     * @dataProvider setProvider
     */
    public function canSetAtCurrentIndex(SequenceIterator $iterator, int $index, int $element, ?int $displacedElement, bool $throws): void
    {
        if ($throws) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $iterator->count() - 1
            ));
        }

        $iterator->seek($index);
        $iterator->set($element);

        self::assertEquals($element, $iterator->current());
        $iterator->next();

        if ($displacedElement === null) {
            self::assertFalse($iterator->valid());
        } else {
            self::assertEquals($displacedElement, $iterator->current());
        }
    }

    public function setAllProvider(): array
    {
        return [
            'Put [14, 68, 99] at 3'               => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 3, [14, 68, 99], 6, false],
            'Put [444, 444, 12] at 9'             => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 9, [444, 444, 12], null, true],
            'Put [-7000, -1000, -2000] at 8'      => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 8, [-7000, -1000, -2000], null, false],
            'Put [32, 64] at 1'                   => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], -1, [32, 64], null, true],
            'Put [9000, 9001, 9002, 9003] at 100' => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 100, [9000, 9001, 9002, 9003], null, true],
        ];
    }

    /**
     * @test
     * @dataProvider setAllProvider
     */
    public function canSetAllAtCurrentIndex(array $sequenceElements, int $index, array $elements, ?int $displacedElement, bool $throws): void
    {
        $iterator = new SequenceIterator(new Sequence($sequenceElements));

        if ($throws) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $iterator->count() - 1
            ));
        }

        $iterator->seek($index);
        $iterator->setAll($elements);

        foreach ($elements as $element) {
            self::assertEquals($element, $iterator->current());
            $iterator->next();
        }

        if ($displacedElement === null) {
            self::assertFalse($iterator->valid());
        } else {
            self::assertEquals($displacedElement, $iterator->current());
        }
    }

    public function findProvider(): array
    {
        $iterator = new SequenceIterator(new Sequence([1, 4, 7, 234, 9, 5345, 6, 1, 9]));

        return [
            'Find 1 from 0'     => [$iterator, 1, 0, 0],
            'Find 1 from 4'     => [$iterator, 1, 4, 7],
            'Find 6 from 8'     => [$iterator, 6, 8, null],
            'Find 234 from 3'   => [$iterator, 234, 3, 3],
            'Find 11000 from 2' => [$iterator, 11000, 2, null],
        ];
    }

    /**
     * @test
     * @dataProvider findProvider
     */
    public function canFindElement(SequenceIterator $iterator, int $element, int $index, ?int $result): void
    {
        $iterator->seek($index);

        if ($result !== null) {
            self::assertTrue($iterator->find($element));
            self::assertSame($result, $iterator->key());
            self::assertSame($element, $iterator->current());
        } else {
            self::assertFalse($iterator->find($element));
        }
    }

    public function unsetProvider(): array
    {
        return [
            'Unset at 3'   => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 3, 9, false],
            'Unset at 9'   => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 9, null, true],
            'Unset at 8'   => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 8, null, false],
            'Unset at 1'   => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 1, 7, false],
            'Unset at 100' => [[1, 4, 7, 234, 9, 5345, 6, 1, 9], 100, null, true],
        ];
    }

    /**
     * @test
     * @dataProvider unsetProvider
     */
    public function canUnsetAtCurrentIndex(array $sequenceElements, int $index, ?int $elementInPlace, bool $throws): void
    {
        $iterator = new SequenceIterator(new Sequence($sequenceElements));

        if ($throws) {
            $this->expectException(OutOfRangeException::class);
            $this->expectExceptionMessage(sprintf(
                'The provided index %s is outside the required range of %s <> %s',
                $index,
                0,
                $iterator->count() - 1
            ));
        }

        $iterator->seek($index);
        $iterator->unset();

        if ($elementInPlace === null) {
            self::assertFalse($iterator->valid());
        } else {
            self::assertEquals($elementInPlace, $iterator->current());
        }
    }

    public function exceptionThrownProvider()
    {
        $iterator = new SequenceIterator(new Sequence([1, 4, 7, 234, 9, 5345, 6, 1, 9]));
        $iterator->seek(8);
        $iterator->next();

        return [
            'Throws put'    => [$iterator, 'put', [1]],
            'Throws putAll' => [$iterator, 'putAll', [[1, 4]]],
            'Throws set'    => [$iterator, 'set', [1]],
            'Throws setAll' => [$iterator, 'setAll', [[1, 4]]],
        ];
    }

    /**
     * @test
     * @dataProvider exceptionThrownProvider
     */
    public function throwsExceptionsWhenThereIsNoKey(SequenceIterator $iterator, string $method, array $args): void
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage(sprintf(
            'The provided index %s is outside the required range of %s <> %s',
            9,
            0,
            8
        ));

        $iterator->{$method}(...$args);
    }
}