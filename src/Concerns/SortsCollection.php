<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Helpers\ComparisonHelper;
use Smpl\Utils\Support\Comparators;
use Smpl\Utils\Support\Predicates;
use function Smpl\Utils\get_sign;

/**
 * Sorts Collection Concerns
 *
 * This concern exists as an implementation of {@see \Smpl\Collections\Contracts\SortedCollection}
 * to simplify its usage, as the implementation will pretty much be the same
 * in all cases.
 *
 * @template I of mixed
 * @template E of mixed
 * @requires \Smpl\Collections\Contracts\SortedCollection<I, E>
 * @mixin \Smpl\Collections\Contracts\Collection<I, E>
 */
trait SortsCollection
{
    /**
     * @param E $element
     *
     * @return bool
     */
    public function add(mixed $element): bool
    {
        /** @psalm-suppress MixedArgument */
        $result = parent::add($element);

        $this->sort();

        return $result;
    }

    /**
     * @return \Smpl\Utils\Contracts\Comparator<E>
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidNullableReturnType
     */
    public function getComparator(): Comparator
    {
        if (! isset($this->comparator)) {
            $this->setComparator(Comparators::default());
        }

        return $this->comparator;
    }

    /**
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return static
     *
     * @psalm-suppress MethodSignatureMismatch
     */
    public function setComparator(?Comparator $comparator = null): static
    {
        parent::setComparator($comparator);

        $this->sort();

        return $this;
    }

    /**
     * @param E $element
     *
     * @return E|null
     */
    public function ceiling(mixed $element): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $elements = $this->getElementsThatAre(
            $element,
            Predicates::or(
                Predicates::id(ComparisonHelper::MORE_THAN),
                Predicates::id(ComparisonHelper::EQUAL_TO)
            )
        );

        if (! empty($elements)) {
            return $elements[0];
        }

        return null;
    }

    /**
     * @param E $element
     *
     * @return E|null
     */
    public function floor(mixed $element): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $elements = $this->getElementsThatAre(
            $element,
            Predicates::or(
                Predicates::id(ComparisonHelper::LESS_THAN),
                Predicates::id(ComparisonHelper::EQUAL_TO)
            )
        );

        if (! empty($elements)) {
            return array_pop($elements);
        }

        return null;
    }

    /**
     * @param E    $toElement
     * @param bool $inclusive
     *
     * @return static
     */
    public function headset(mixed $toElement, bool $inclusive = false): static
    {
        if ($this->isEmpty()) {
            return $this->copy([]);
        }

        if ($inclusive) {
            $elements = $this->getElementsThatAre(
                $toElement,
                Predicates::or(
                    Predicates::id(ComparisonHelper::LESS_THAN),
                    Predicates::id(ComparisonHelper::EQUAL_TO)
                )
            );
        } else {
            $elements = $this->getElementsThatAre(
                $toElement,
                Predicates::id(ComparisonHelper::LESS_THAN)
            );
        }

        return $this->copy($elements);
    }

    /**
     * @param E $element
     *
     * @return E|null
     */
    public function higher(mixed $element): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $elements = $this->getElementsThatAre(
            $element,
            Predicates::id(ComparisonHelper::MORE_THAN)
        );

        if (! empty($elements)) {
            return $elements[0];
        }

        return null;
    }

    /**
     * @param E $element
     *
     * @return E|null
     */
    public function lower(mixed $element): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $elements = $this->getElementsThatAre(
            $element,
            Predicates::id(ComparisonHelper::LESS_THAN)
        );

        if (! empty($elements)) {
            return array_pop($elements);
        }

        return null;
    }

    /**
     * @param E    $fromElement
     * @param E    $toElement
     * @param bool $fromInclusive
     * @param bool $toInclusive
     *
     * @return static
     */
    public function subset(mixed $fromElement, mixed $toElement, bool $fromInclusive = false, bool $toInclusive = false): static
    {
        if ($this->isEmpty()) {
            return $this->copy([]);
        }

        $elements = [];

        if ($fromInclusive) {
            $fromPredicate = Predicates::or(
                Predicates::id(ComparisonHelper::EQUAL_TO),
                Predicates::id(ComparisonHelper::MORE_THAN)
            );
        } else {
            $fromPredicate = Predicates::id(ComparisonHelper::MORE_THAN);
        }

        if ($toInclusive) {
            $toPredicate = Predicates::or(
                Predicates::id(ComparisonHelper::EQUAL_TO),
                Predicates::id(ComparisonHelper::LESS_THAN)
            );
        } else {
            $toPredicate = Predicates::id(ComparisonHelper::LESS_THAN);
        }

        foreach ($this->elements as $existingElement) {
            $fromResult = get_sign($this->getComparator()->compare($existingElement, $fromElement));
            $toResult   = get_sign($this->getComparator()->compare($existingElement, $toElement));

            if ($fromPredicate->test($fromResult) && $toPredicate->test($toResult)) {
                $elements[] = $existingElement;
            }
        }

        return $this->copy($elements);
    }

    /**
     * @param E    $fromElement
     * @param bool $inclusive
     *
     * @return static
     */
    public function tailset(mixed $fromElement, bool $inclusive = false): static
    {
        if ($this->isEmpty()) {
            return $this->copy([]);
        }

        if ($inclusive) {
            $elements = $this->getElementsThatAre(
                $fromElement,
                Predicates::or(
                    Predicates::id(ComparisonHelper::MORE_THAN),
                    Predicates::id(ComparisonHelper::EQUAL_TO)
                )
            );
        } else {
            $elements = $this->getElementsThatAre(
                $fromElement,
                Predicates::id(ComparisonHelper::MORE_THAN)
            );
        }

        return $this->copy($elements);
    }

    /**
     * Get elements that use the provided predicate on the comparator result of element.
     *
     * This method returns a subset of the elements contained within this
     * collection, where the result of {@see \Smpl\Utils\Contracts\Comparator::compare()}
     * against $element, passes $filter.
     *
     * @param E                               $element
     * @param \Smpl\Utils\Contracts\Predicate $filter
     *
     * @return list<E>
     */
    private function getElementsThatAre(mixed $element, Predicate $filter): array
    {
        $elements = [];

        foreach ($this->elements as $existingElement) {
            $result = get_sign($this->getComparator()->compare($existingElement, $element));

            if ($filter($result)) {
                $elements[] = $existingElement;
            }
        }

        /** @infection-ignore-all */
        if (! empty($elements)) {
            usort($elements, $this->getComparator());
        }

        return $elements;
    }

    /**
     * Sort the elements of this collection.
     *
     * @return void
     */
    private function sort(): void
    {
        usort($this->elements, $this->getComparator());
    }
}