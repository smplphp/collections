<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Collection;
use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Collections\Helpers\IterableHelper;
use Smpl\Collections\Support\PrioritisedElement;
use Smpl\Collections\Support\PrioritisedElementComparator;
use Smpl\Collections\Support\PriorityCollectionFlags;
use Smpl\Collections\Support\PriorityComparator;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Helpers\ComparisonHelper;
use Smpl\Utils\Support\Comparators;
use function Smpl\Utils\is_sign_equal_to;

/**
 * @template I of mixed
 * @template E of mixed
 *
 * @requires \Smpl\Collections\Contracts\PrioritisedCollection<I, E>
 */
trait PrioritisesElements
{
    /**
     * @var \Smpl\Collections\Support\PrioritisedElementComparator<E>|null
     */
    protected ?PrioritisedElementComparator $prioritisedElementComparator;

    /**
     * @var \Smpl\Collections\Support\PriorityComparator
     */
    protected PriorityComparator $priorityComparator;

    /**
     * @var int
     */
    protected int $flags;

    /**
     * @param iterable<E>|iterable<\Smpl\Collections\Support\PrioritisedElement<E>>|null $elements
     * @param \Smpl\Utils\Contracts\Comparator|null                                      $comparator
     * @param int                                                                        $flags
     *
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    public function __construct(iterable $elements = null, ?Comparator $comparator = null, int $flags = null)
    {
        $this->flags = $this->normaliseFlags($flags);

        /** @psalm-suppress InvalidArgument */
        parent::__construct($elements, $comparator);
    }

    /**
     * @param E              $element
     * @param int|false|null $priority
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    public function add(mixed $element, int|false|null $priority = null): bool
    {
        /** @infection-ignore-all */
        if (($this->flags & PriorityCollectionFlags::NO_NULL) && $element === null) {
            throw InvalidArgumentException::notNullable();
        }

        if ($priority === false) {
            $priority = null;
        }

        $this->elements[] = $this->wrapElement($element, $priority);
        $this->modifyCount(1);

        $this->prioritiseElements();

        return true;
    }

    /**
     * @param iterable<E>    $elements
     * @param int|false|null $priority
     *
     * @return bool
     */
    public function addAll(iterable $elements, int|false|null $priority = null): bool
    {
        if ($priority === false) {
            $priority = null;
        }

        foreach ($elements as $element) {
            /** @infection-ignore-all */
            if (($this->flags & PriorityCollectionFlags::NO_NULL) && $element === null) {
                throw InvalidArgumentException::notNullable();
            }

            $this->elements[] = $this->wrapElement($element, $priority);
            $this->modifyCount(1);
        }

        $this->prioritiseElements();

        return true;
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function contains(mixed $element): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        /** @psalm-suppress InvalidArgument */
        return IterableHelper::contains($this->elements, $element, $this->getPrioritisedElementComparator());
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function containsAll(iterable $elements): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        /** @psalm-suppress InvalidArgument */
        return IterableHelper::containsAll($this->elements, $elements, $this->getPrioritisedElementComparator());
    }

    /**
     * @param E $element
     *
     * @return int<0,max>
     */
    public function countOf(mixed $element): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        /** @psalm-suppress InvalidArgument */
        return IterableHelper::countOf($this->elements, $element, $this->getPrioritisedElementComparator());
    }

    /**
     * @param \Smpl\Utils\Contracts\Comparator|null $comparator
     *
     * @return static
     *
     * @psalm-suppress MethodSignatureMismatch
     */
    public function setComparator(?Comparator $comparator = null): static
    {
        if ($comparator !== null) {
            /** @infection-ignore-all */
            $this->getPrioritisedElementComparator()->setComparator($comparator);
        } else {
            $this->prioritisedElementComparator = null;
        }

        parent::setComparator($comparator);

        return $this;
    }

    /**
     * @param E $element
     *
     * @return int|null|false
     */
    public function priority(mixed $element): int|null|false
    {
        $comparator = $this->getPrioritisedElementComparator();

        foreach ($this->elements as $existingElement) {
            if (is_sign_equal_to($comparator->compare($existingElement, $element))) {
                return $existingElement->getPriority();
            }
        }

        return false;
    }

    /**
     * @param E $element
     *
     * @return bool
     */
    public function remove(mixed $element): bool
    {
        $modified   = false;
        $comparator = $this->getComparator() ?? new IdenticalityComparator();

        foreach ($this->elements as $index => $existingElement) {
            if ($comparator->compare($existingElement->getElement(), $element) === ComparisonHelper::EQUAL_TO) {
                $this->removeElementByIndex($index);
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return bool
     */
    public function removeIf(Predicate $filter): bool
    {
        $modified = false;

        foreach ($this->elements as $index => $element) {
            if ($filter->test($element->getElement())) {
                $this->removeElementByIndex($index);
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return bool
     */
    public function retainAll(iterable $elements): bool
    {
        $modified   = false;
        $collection = new Collection($elements, $this->getComparator());

        foreach ($this->elements as $element) {
            if (! $collection->contains($element->getElement()) && $this->remove($element->getElement())) {
                $modified = true;
            }
        }

        return $modified;
    }

    /**
     * @return \Smpl\Collections\Support\PrioritisedElementComparator<E>
     */
    private function getPrioritisedElementComparator(): PrioritisedElementComparator
    {
        if (! isset($this->prioritisedElementComparator)) {
            /**
             * @psalm-suppress PossiblyNullArgument
             * @psalm-suppress InvalidArgument
             * @infection-ignore-all
             */
            $this->prioritisedElementComparator = new PrioritisedElementComparator(
                $this->getComparator() ?? Comparators::identicality()
            );
        }

        return $this->prioritisedElementComparator;
    }

    /**
     * @return \Smpl\Collections\Support\PriorityComparator
     */
    private function getPriorityComparator(): PriorityComparator
    {
        if (! isset($this->priorityComparator)) {
            $this->priorityComparator = new PriorityComparator($this->flags);
        }

        return $this->priorityComparator;
    }

    /**
     * @param int|null $flags
     *
     * @return int
     */
    private function normaliseFlags(?int $flags): int
    {
        if ($flags === null) {
            $flags = 0;
        }

        // Default to ascending order
        if (($flags & PriorityCollectionFlags::ASC_ORDER) === 0 && ($flags & PriorityCollectionFlags::DESC_ORDER) === 0) {
            $flags |= PriorityCollectionFlags::ASC_ORDER;
        }

        // Default to elements without priority being at the end
        if (($flags & PriorityCollectionFlags::NO_PRIORITY_FIRST) === 0 && ($flags & PriorityCollectionFlags::NO_PRIORITY_LAST) === 0) {
            $flags |= PriorityCollectionFlags::NO_PRIORITY_LAST;
        }

        // If both ascending and descending order have been provided, error
        if (
            ($flags & PriorityCollectionFlags::ASC_ORDER) === PriorityCollectionFlags::ASC_ORDER
            && ($flags & PriorityCollectionFlags::DESC_ORDER) === PriorityCollectionFlags::DESC_ORDER
        ) {
            throw InvalidArgumentException::priorityFlagsOrder();
        }

        // If both no priority first and last have been provided, error
        if (
            ($flags & PriorityCollectionFlags::NO_PRIORITY_FIRST) === PriorityCollectionFlags::NO_PRIORITY_FIRST
            && ($flags & PriorityCollectionFlags::NO_PRIORITY_LAST) === PriorityCollectionFlags::NO_PRIORITY_LAST
        ) {
            throw InvalidArgumentException::priorityFlagsPlacement();
        }

        // If both null value first and last have been provided, error
        if (
            ($flags & PriorityCollectionFlags::NULL_VALUE_FIRST) === PriorityCollectionFlags::NULL_VALUE_FIRST
            && ($flags & PriorityCollectionFlags::NULL_VALUE_LAST) === PriorityCollectionFlags::NULL_VALUE_LAST
        ) {
            throw InvalidArgumentException::priorityFlagsNull();
        }

        return $flags;
    }

    /**
     * @return void
     */
    private function prioritiseElements(): void
    {
        /** @psalm-suppress InvalidArgument */
        usort($this->elements, $this->getPriorityComparator());
    }

    /**
     * @param E|\Smpl\Collections\Support\PrioritisedElement<E> $element
     * @param int|null                                          $priority
     *
     * @return \Smpl\Collections\Support\PrioritisedElement
     */
    private function wrapElement(mixed $element, ?int $priority): PrioritisedElement
    {
        return $element instanceof PrioritisedElement ? $element : new PrioritisedElement($element, $priority);
    }

    /**
     * @return int
     */
    public function flags(): int
    {
        return $this->flags;
    }

    /**
     * @return list<E>
     */
    public function toArray(): array
    {
        return array_map(
            static function (PrioritisedElement $element): mixed {
                return $element->getElement();
            },
            $this->elements
        );
    }
}