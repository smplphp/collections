<?php
/** @noinspection TraitsPropertiesConflictsInspection */
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Concerns\PrioritisesElements;
use Smpl\Utils\Contracts\Comparator;

/**
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
     *
     * @return \Smpl\Collections\PriorityDeque<NE>
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
        return new PriorityDeque($elements ?? $this->toArray(), $this->getComparator(), $this->flags());
    }

    /**
     * @return \Smpl\Collections\Contracts\PriorityQueue
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function asQueue(): Contracts\PriorityQueue
    {
        return new PriorityQueue($this->elements, $this->getComparator());
    }

    /**
     * @return \Smpl\Collections\Contracts\PriorityStack
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function asStack(): Contracts\PriorityStack
    {
        return new PriorityStack($this->elements, $this->getComparator());
    }
}