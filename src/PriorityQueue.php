<?php
/** @noinspection TraitsPropertiesConflictsInspection */
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\PrioritisesElements;

/**
 * Priority Queue
 *
 * This is an extension of {@see \Smpl\Collections\Contracts\Queue} that allows
 * you to add elements with a priority.
 *
 * This isn't a FIFO (first-in-first-out) implementation, instead elements
 * added to the queue are ordered using their priority.
 *
 * @template       E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\PriorityQueue<E>
 *
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress MixedInferredReturnType
 */
final class PriorityQueue extends BaseCollection implements Contracts\PriorityQueue
{
    use PrioritisesElements;

    /**
     * @var list<\Smpl\Collections\Support\PrioritisedElement<E>>
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected array $elements;

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
     * @template       NE of mixed
     *
     * @param iterable<NE>|null $elements
     * @param int|null          $flags
     *
     * @return \Smpl\Collections\PriorityQueue<NE|E>
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
        return new PriorityQueue($elements ?? $this->toArray(), $this->getComparator(), $flags ?? $this->flags);
    }
}