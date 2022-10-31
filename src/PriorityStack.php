<?php
/** @noinspection TraitsPropertiesConflictsInspection */
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\PrioritisesElements;
use Smpl\Collections\Support\PrioritisedElement;
use Smpl\Utils\Comparators\IdenticalityComparator;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Helpers\ComparisonHelper;

/**
 * Priority Queue
 *
 * This is an extension of {@see \Smpl\Collections\Contracts\Stack} that allows
 * you to add elements with a priority.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseCollection<E>
 * @implements \Smpl\Collections\Contracts\PriorityStack<E>
 *
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress MixedInferredReturnType
 */
final class PriorityStack extends BaseCollection implements Contracts\PriorityStack
{
    use PrioritisesElements;

    /**
     * @var list<\Smpl\Collections\Support\PrioritisedElement<E>>
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected array $elements;

    /**
     * @param iterable<E>|null                      $elements
     * @param \Smpl\Utils\Contracts\Comparator|null $comparator
     * @param int                                   $flags
     *
     * @noinspection PhpDocSignatureInspection
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(iterable $elements = null, ?Comparator $comparator = null, int $flags = null)
    {
        $this->flags = $this->normaliseFlags($flags);

        if ($elements !== null) {
            $this->addAll($elements);
        }

        $this->setComparator($comparator);
    }

    /**
     * @return E|null
     */
    public function peekLast(): mixed
    {
        return ($this->elements[$this->getMaxIndex()] ?? null)?->getElement();
    }

    /**
     * @return E|null
     */
    public function pollLast(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        /** @var PrioritisedElement<E> $element */
        $element = array_pop($this->elements);

        $this->modifyCount(-1);

        return $element->getElement();
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
     * @template       NE of mixed
     *
     * @param iterable<NE>|null $elements
     * @param int|null          $flags
     *
     * @return \Smpl\Collections\PriorityStack<NE|E>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     *
     * @noinspection   PhpDocSignatureInspection
     * @noinspection   PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null, ?int $flags = null): static
    {
        return new PriorityStack($elements ?? $this->toArray(), $this->getComparator(), $flags ?? $this->flags);
    }
}