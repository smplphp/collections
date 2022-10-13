<?php

namespace Smpl\Collections\Contracts;

/**
 * Compares Values Contract
 *
 * This contract is for objects that need to compare two values, and could
 * therefore benefit from a {@see \Smpl\Collections\Contracts\Comparator}.
 *
 * @template E of mixed
 */
interface ComparesValues
{
    /**
     * Set the comparator this object should use for comparing values.
     *
     * This method will let you set a custom comparator that this object
     * will use to determine how values compare to each other.
     *
     * @param \Smpl\Collections\Contracts\Comparator<E>|null $comparator
     *
     * @return static
     */
    public function setComparator(?Comparator $comparator = null): static;

    /**
     * Get the comparator this object uses for comparison.
     *
     * Get the comparator that this object uses to compare values. Some
     * implementations will fall back to a default implementation, whereas
     * others will have their own logic for comparing values.
     *
     * @return \Smpl\Collections\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator;
}