<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

/**
 * Prioritised Element
 *
 * This class is a wrapper that contains an element and its priority, and is
 * used in {@see \Smpl\Collections\PriorityQueue}.
 *
 * @template E of mixed
 * @internal
 */
final class PrioritisedElement
{
    /**
     * @var E
     */
    private mixed $element;

    /**
     * @var int|null
     */
    private ?int $priority;

    /**
     * @param E $element
     */
    public function __construct(mixed $element, ?int $priority = null)
    {
        $this->element  = $element;
        $this->priority = $priority;
    }

    /**
     * Get the prioritised element.
     *
     * @return E
     */
    public function getElement(): mixed
    {
        return $this->element;
    }

    /**
     * Get the priority of the element.
     *
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Check if the element is null.
     *
     * @return bool
     */
    public function isNull(): bool
    {
        return $this->getElement() === null;
    }

    /**
     * Check if the element has a priority.
     *
     * @return bool
     */
    public function hasPriority(): bool
    {
        return $this->getPriority() !== null;
    }
}