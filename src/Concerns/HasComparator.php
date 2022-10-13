<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Contracts\Comparator;

/**
 * Has Comparator Concern
 *
 * This concern exists to provide a base implementation of
 * {@see \Smpl\Collections\Contracts\ComparesValues} with the intention of
 * avoiding boilerplate.
 *
 * @template E of mixed
 */
trait HasComparator
{
    /**
     * @var \Smpl\Collections\Contracts\Comparator<E>|null
     */
    private ?Comparator $comparator = null;

    /**
     * @return \Smpl\Collections\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->comparator;
    }

    /**
     * @param \Smpl\Collections\Contracts\Comparator<E>|null $comparator
     *
     * @return static
     */
    public function setComparator(?Comparator $comparator = null): static
    {
        $this->comparator = $comparator;

        return $this;
    }
}