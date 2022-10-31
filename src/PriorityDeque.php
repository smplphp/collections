<?php
/** @noinspection TraitsPropertiesConflictsInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Iterator;
use Smpl\Collections\Concerns\PrioritisesElements;
use Smpl\Collections\Iterators\SimpleIterator;
use Smpl\Collections\Support\PrioritisedElement;

/**
 * Priority Deque
 *
 * This is an extension of {@see \Smpl\Collections\Contracts\Deque} that allows
 * you to add elements with a priority.
 *
 * This isn't a FIFO (first-in-first-out), or LIFO (last-in-first-out) implementation,
 * instead elements are added and then ordered using their priority.
 *
 * @template E of mixed
 * @extends \Smpl\Collections\BaseDeque<E>
 * @implements \Smpl\Collections\Contracts\PriorityDeque<E>
 */
final class PriorityDeque extends BaseDeque implements Contracts\PriorityDeque
{
    use PrioritisesElements;

    /**
     * @var list<\Smpl\Collections\Support\PrioritisedElement<E>>
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected array $elements;

    /**
     * @param E              $element
     * @param int|false|null $priority
     *
     * @return bool
     */
    public function addFirst(mixed $element, int|false|null $priority = false): bool
    {
        return $this->add($element, $priority);
    }

    /**
     * @param E              $element
     * @param int|false|null $priority
     *
     * @return bool
     *
     * @noinspection SenselessMethodDuplicationInspection
     */
    public function addLast(mixed $element, int|false|null $priority = false): bool
    {
        return $this->add($element);
    }

    /**
     * @template       NE of mixed
     *
     * @param iterable<NE|E>|null $elements
     * @param int|null            $flags
     *
     * @return \Smpl\Collections\PriorityDeque<NE>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MismatchingDocblockReturnType
     * @psalm-suppress InvalidArgument
     *
     * @noinspection   PhpDocSignatureInspection
     * @noinspection   PhpUnnecessaryStaticReferenceInspection
     */
    public function copy(iterable $elements = null, ?int $flags = null): static
    {
        return new PriorityDeque($elements ?? $this->elements, $this->getComparator(), $flags ?? $this->flags());
    }

    /**
     * @return E|null
     */
    public function peekFirst(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->elements[0]->getElement();
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
    public function pollFirst(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        /** @infection-ignore-all */
        $element = array_shift($this->elements)?->getElement();

        $this->modifyCount(-1);

        return $element;
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
     * @return \Smpl\Collections\Contracts\Queue
     */
    public function asQueue(): Contracts\Queue
    {
        return new Queue($this->toArray(), $this->getComparator());
    }

    /**
     * @return \Smpl\Collections\Contracts\Stack
     */
    public function asStack(): Contracts\Stack
    {
        return new Stack($this->toArray(), $this->getComparator());
    }

    /**
     * @return \Smpl\Collections\Contracts\PriorityQueue
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @psalm-suppress InvalidArgument
     */
    public function asPriorityQueue(): Contracts\PriorityQueue
    {
        return new PriorityQueue($this->elements, $this->getComparator());
    }

    /**
     * @return \Smpl\Collections\Contracts\PriorityStack
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @psalm-suppress InvalidArgument
     */
    public function asPriorityStack(): Contracts\PriorityStack
    {
        return new PriorityStack($this->elements, $this->getComparator());
    }

    /**
     * @return \Iterator<int, E>
     */
    public function ascendingIterator(): Iterator
    {
        return new SimpleIterator($this->toArray());
    }

    /**
     * @return \Iterator<int, E>
     */
    public function descendingIterator(): Iterator
    {
        return new SimpleIterator(array_reverse($this->toArray()));
    }
}