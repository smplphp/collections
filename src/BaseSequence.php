<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Contracts\Set;
use Smpl\Collections\Exceptions\OutOfRangeException;
use Smpl\Collections\Iterators\SequenceIterator;
use function Smpl\Utils\is_sign_equal_to;

/**
 * Base Sequence
 *
 * This class forms the base for sequence collections, preventing the need to duplicate
 * code and method definitions where inheritance will suffice.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 * @implements \Smpl\Collections\Contracts\Sequence<E>
 */
abstract class BaseSequence extends BaseCollection implements Contracts\Sequence
{
    /**
     * @param E          $element
     * @param int<0,max> $index
     *
     * @return int<0,max>|null
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function find(mixed $element, int $index): ?int
    {
        if ($this->isOutsideOfRange($index)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if ($this->isEmpty()) {
            return null;
        }

        // If this collection has a comparator, we'll need to use that for matching
        // elements.
        $comparator = $this->getComparator();

        // If the provided index is greater than 0, we'll want to get a subset to
        // work with, otherwise we can just use the current element.
        /** @infection-ignore-all */
        $elements = $index > 0 ? $this->sliceElements($index, preserveKeys: true) : $this->elements;

        if ($comparator === null) {
            /** @var int<0,max>|false $foundIndex */
            $foundIndex = array_search($element, $elements, true);

            /** @psalm-suppress LessSpecificReturnStatement */
            return is_int($foundIndex) ? $foundIndex : null;
        }

        /**
         * @var int<0,max> $elementIndex
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        foreach ($elements as $elementIndex => $existingElement) {
            if (is_sign_equal_to($comparator->compare($existingElement, $element))) {
                return $elementIndex;
            }
        }

        return null;
    }

    /**
     * @return E|null
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function first(): mixed
    {
        return $this->get(0);
    }

    /**
     * @param int<-1,max> $index
     *
     * @return E|null
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function get(int $index): mixed
    {
        if ($index === -1) {
            $index = $this->getMaxIndex();
        }

        if ($this->isOutsideOfRange($index)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if ($this->isEmpty()) {
            return null;
        }

        return $this->elements[$index] ?? null;
    }

    /**
     * @param int $index
     *
     * @return bool
     */
    public function has(int $index): bool
    {
        return isset($this->elements[$index]);
    }

    /**
     * @param E $element
     *
     * @return int<0,max>|null
     */
    public function indexOf(mixed $element): ?int
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->find($element, 0);
    }

    /**
     * @param E $element
     *
     * @return \Smpl\Collections\Contracts\Set<int>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function indexesOf(mixed $element): Set
    {
        if ($this->isEmpty()) {
            /** @psalm-suppress MixedReturnTypeCoercion */
            return new ImmutableSet();
        }

        $comparator = $this->getComparator();
        $indexes    = [];

        if ($comparator === null) {
            $indexes = array_keys($this->elements, $element, true);
        } else {
            foreach ($this->elements as $index => $existingElement) {
                if (is_sign_equal_to($comparator->compare($existingElement, $element))) {
                    $indexes[] = $index;
                }
            }
        }

        return new ImmutableSet($indexes);
    }

    /**
     * @param E $element
     *
     * @return int<0,max>|null
     */
    public function lastIndexOf(mixed $element): ?int
    {
        if ($this->isEmpty()) {
            return null;
        }

        $comparator = $this->getComparator();

        if ($comparator === null) {
            /** @var list<int<0,max>> $indexes */
            $indexes = array_keys($this->elements, $element, true);
            return array_pop($indexes);
        }

        $lastIndex = null;

        /**
         * @var int<0,max> $index
         */
        foreach ($this->elements as $index => $existingElement) {
            if (is_sign_equal_to($comparator->compare($existingElement, $element))) {
                $lastIndex = $index;
            }
        }

        return $lastIndex;
    }

    /**
     * @return E|null
     */
    public function last(): mixed
    {
        return $this->get(-1);
    }

    /**
     * @param int<-1,max> $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[$offset]);
    }

    /**
     * @param int<-1,max> $offset
     *
     * @return mixed
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function offsetGet(mixed $offset): mixed
    {
        // This is here so that you can't access an array index of -1 when
        // treating this like an array, because get() allows that.
        if ($this->isOutsideOfRange($offset)) {
            throw OutOfRangeException::index($offset, 0, $this->getMaxIndex());
        }

        return $this->get($offset);
    }

    /**
     * @param int<0,max> $offset
     * @param E          $value
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @param int<0,max> $offset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }

    /**
     * @param int $index
     * @param E   $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function put(int $index, mixed $element): static
    {
        if ($this->isOutsideOfRange($index, true)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if (! $this->isEmpty()) {
            if ($index > $this->getMaxIndex()) {
                $this->elements[] = $element;
            } else {
                $remainingElements = array_splice($this->elements, $index);
                $this->elements    = array_merge(
                    $this->elements,
                    [$element],
                    $remainingElements
                );
            }
            $this->count++;
        }

        return $this;
    }

    /**
     * @param int         $index
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function putAll(int $index, iterable $elements): static
    {
        if ($this->isOutsideOfRange($index, true)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if (! $this->isEmpty()) {
            if ($index <= $this->getMaxIndex()) {
                $remainingElements = array_splice($this->elements, $index);
            } else {
                $remainingElements = [];
            }

            foreach ($elements as $element) {
                $this->elements[] = $element;
                $this->count++;
            }

            if (! empty($remainingElements)) {
                $this->elements = array_merge(
                    $this->elements,
                    $remainingElements
                );
            }
        }

        return $this;
    }

    /**
     * @param int $index
     * @param E   $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function set(int $index, mixed $element): static
    {
        if ($this->isOutsideOfRange($index, true)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if (! $this->isEmpty()) {
            /** @psalm-suppress PropertyTypeCoercion */
            $this->elements[$index] = $element;

            if ($index > $this->getMaxIndex()) {
                $this->count++;
            }
        }

        return $this;
    }

    /**
     * @param int         $index
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function setAll(int $index, iterable $elements): static
    {
        if ($this->isOutsideOfRange($index, true)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if (! $this->isEmpty()) {
            foreach ($elements as $element) {
                /** @psalm-suppress PropertyTypeCoercion */
                $this->elements[$index] = $element;

                if ($index > $this->getMaxIndex()) {
                    $this->count++;
                }

                $index++;
            }
        }

        return $this;
    }

    /**
     * @param int      $index
     * @param int|null $length
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function subset(int $index, int $length = null): static
    {
        if ($this->isOutsideOfRange($index)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if ($length !== null) {
            $lastIndex = $index + $length;

            if ($this->isOutsideOfRange($lastIndex, true)) {
                /**
                 * @psalm-suppress PossiblyNullArgument
                 */
                throw OutOfRangeException::subsetLength($index, $length, 0, $this->getMaxIndex());
            }
        }

        if ($this->isEmpty()) {
            $elements = [];
        } else {
            $elements = $this->sliceElements($index, $length);
        }

        /** @psalm-suppress InvalidArgument */
        return $this->copy($elements);
    }

    /**
     * @return static
     */
    public function tail(): static
    {
        return $this->subset(1);
    }

    /**
     * @param int<0,max> $index
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\OutOfRangeException
     */
    public function unset(int $index): static
    {
        if ($this->isOutsideOfRange($index)) {
            throw OutOfRangeException::index($index, 0, $this->getMaxIndex());
        }

        if (! $this->isEmpty()) {
            unset($this->elements[$index]);
            $this->setCount($this->count - 1);
            /** @infection-ignore-all */
            $this->elements = array_values($this->elements);
        }

        return $this;
    }

    /**
     * Get an iterator for this collection.
     *
     * This method returns an iterator specifically created for iterating
     * sequences.
     *
     * @return \Smpl\Collections\Iterators\SequenceIterator<E>
     */
    public function getSequenceIterator(): SequenceIterator
    {
        return new SequenceIterator($this);
    }

    /**
     * Get this collections max index.
     *
     * This method will return the maximum index for the elements currently
     * stored in this collection.
     *
     * @return int<0, max>
     *
     * @internal
     *
     * @infection-ignore-all
     */
    protected function getMaxIndex(): int
    {
        return max(0, $this->count() - 1);
    }

    /**
     * Check if the provided index is outside this collections range.
     *
     * This method will check to see if $index is within the bounds of
     * 0 <> (count() -1), if $allowNew is false. If $allowNew is true, the range
     * will be 0 <> count().
     *
     * @param int  $index
     * @param bool $allowNew
     *
     * @return bool
     *
     * @internal
     *
     * @infection-ignore-all
     */
    protected function isOutsideOfRange(int $index, bool $allowNew = false): bool
    {
        $maxIndex = $this->getMaxIndex();

        if ($allowNew) {
            ++$maxIndex;
        }

        return $index < 0 || $index > $maxIndex;
    }

    /**
     * Get a slice of this collections elements.
     *
     * This method returns a slice of the collections' element, optionally preserving
     * keys, using {@see \array_slice()}.
     *
     * @param int      $offset
     * @param int|null $length
     * @param bool     $preserveKeys
     *
     * @return list<E>
     *
     * @internal
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     *
     * @infection-ignore-all
     */
    protected function sliceElements(int $offset, int $length = null, bool $preserveKeys = false): array
    {
        return array_slice($this->elements, $offset, $length, $preserveKeys);
    }
}