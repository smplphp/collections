<?php
/** @noinspection TraitsPropertiesConflictsInspection */
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\PrioritisesElements;
use Smpl\Collections\Support\PrioritisedElement;

/**
 * Priority Stack
 *
 * This is an extension of {@see \Smpl\Collections\Contracts\Stack} that allows
 * you to add elements with a priority.
 *
 * This isn't a LIFE (last-in-first-out) implementation, instead elements
 * added to the stack are ordered using their priority.
 *
 * @template       E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @implements \Smpl\Collections\Contracts\PriorityStack<E>
 *
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress MixedInferredReturnType
 */
final class PriorityStack extends BaseCollection implements Contracts\PriorityStack
{
    /** @use \Smpl\Collections\Concerns\PrioritisesElements<int, E> */
    use PrioritisesElements;

    /**
     * @var list<\Smpl\Collections\Support\PrioritisedElement<E>>
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected array $elements;

    /**
     * @template NE of mixed
     *
     * @param NE ...$elements
     *
     * @return static<NE>
     */
    public static function of(...$elements): static
    {
        return new self($elements);
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
     * @template       NI of array-key
     * @template       NE of mixed
     *
     * @param iterable<NI, NE>|null $elements
     * @param int|null              $flags
     *
     * @return static
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @noinspection   PhpDocSignatureInspection
     */
    public function copy(iterable $elements = null, ?int $flags = null): static
    {
        return new self($elements ?? $this->getElements(), $this->getComparator(), $flags ?? $this->flags());
    }
}