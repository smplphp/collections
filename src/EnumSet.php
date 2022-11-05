<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Exceptions\InvalidArgumentException;
use UnitEnum;

/**
 * Enum Set
 *
 * An implementation of the {@see \Smpl\Collections\Contracts\Set} contract
 * specifically for storing cases of an enum.
 *
 * @template E of \UnitEnum
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\Set<E>
 */
final class EnumSet extends BaseCollection implements Contracts\Set
{
    /**
     * @var class-string<E>
     */
    private string $enumClass;

    /**
     * @param class-string<E>  $enumClass
     * @param iterable<E>|null $elements
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(string $enumClass, iterable $elements = null)
    {
        $this->enumClass = $enumClass;

        parent::__construct($elements);
    }

    /**
     * @template       NE of \UnitEnum
     *
     * @param iterable<NE|E>|null $elements
     *
     * @return \Smpl\Collections\EnumSet<NE>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     *
     * @noinspection   PhpDocSignatureInspection
     * @noinspection   PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null): static
    {
        return new EnumSet($this->enumClass, $elements ?? $this->elements);
    }

    /**
     * @template       NE of UnitEnum
     *
     * @param NE ...$elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @psalm-suppress MethodSignatureMismatch
     * @psalm-suppress DocblockTypeContradiction
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public static function of(mixed ...$elements): static
    {
        /** @var mixed $firstElement */
        $firstElement = $elements[0] ?? null;

        if ($firstElement instanceof UnitEnum) {
            $enumClass = $firstElement::class;

            foreach ($elements as $element) {
                if (! ($element instanceof $enumClass)) {
                    throw InvalidArgumentException::invalidEnumCreation($enumClass);
                }
            }

            return new self($enumClass, $elements);
        }

        throw InvalidArgumentException::noEnum();
    }

    /**
     * Create a new enum set for the provided enum.
     *
     * This method creates a new instance of this collection that can contain
     * only cases of the provided enum.
     *
     * If $enumClass isn't a subclass of {@see \UnitEnum}, an
     * {@see \Smpl\Collections\Exceptions\InvalidArgumentException} will be
     * thrown.
     *
     * @template NE of UnitEnum
     *
     * @param class-string<NE> $enumClass
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public static function noneOf(string $enumClass): static
    {
        if (! is_subclass_of($enumClass, UnitEnum::class)) {
            throw InvalidArgumentException::noEnum();
        }

        return new self($enumClass);
    }

    /**
     * Create a new enum set containing all cases for the provided enum.
     *
     * This method creates a new instance of this collection that contains
     * all cases of the provided enum.
     *
     * If $enumClass isn't a subclass of {@see \UnitEnum}, an
     * {@see \Smpl\Collections\Exceptions\InvalidArgumentException} will be
     * thrown.
     *
     * @template NE of UnitEnum
     *
     * @param class-string<NE> $enumClass
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public static function allOf(string $enumClass): static
    {
        if (! is_subclass_of($enumClass, UnitEnum::class)) {
            throw InvalidArgumentException::noEnum();
        }

        return new self($enumClass, $enumClass::cases());
    }

    /**
     * @param E $element
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public function add(mixed $element): bool
    {
        if (! $this->isValidEnum($element)) {
            throw InvalidArgumentException::invalidEnum($this->enumClass);
        }

        if (! $this->contains($element)) {
            return parent::add($element);
        }

        return false;
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool
    {
        if ($this->isValidEnum($element)) {
            return in_array($element, $this->elements, true);
        }

        return false;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool
    {
        foreach ($elements as $element) {
            if (! $this->contains($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param E $element
     *
     * @return int<0, max>
     */
    public function countOf(mixed $element): int
    {
        return $this->isValidEnum($element) && $this->contains($element) ? 1 : 0;
    }

    /**
     * @param mixed $element
     *
     * @return bool
     */
    public function remove(mixed $element): bool
    {
        if (! $this->isValidEnum($element)) {
            return false;
        }

        foreach ($this->elements as $index => $existingElement) {
            if ($existingElement === $element) {
                $this->removeElementByIndex($index);
                return true;
            }
        }

        return false;
    }

    /**
     * @param iterable<mixed> $elements
     *
     * @return bool
     *
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     */
    public function retainAll(iterable $elements): bool
    {
        $modified    = false;
        $set         = (new Set($elements));
        $newElements = [];

        /** @var mixed $element */
        foreach ($set as $element) {
            if (! $this->isValidEnum($element)) {
                continue;
            }

            /**
             * @var E $element
             */
            if ($this->contains($element)) {
                $newElements[] = $element;
            }

            $modified = true;
        }

        if ($modified === true) {
            $this->elements = $newElements;
            $this->count    = count($newElements);
        }

        return $modified;
    }

    /**
     * Get the enum class for this set.
     *
     * This method returns the enum class that all elements in this set must
     * belong to.
     *
     * @return class-string<E>
     */
    public function getEnumClass(): string
    {
        return $this->enumClass;
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @infection-ignore-all
     */
    protected function isValidEnum(mixed $element): bool
    {
        return $element instanceof $this->enumClass;
    }
}
