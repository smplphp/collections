<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Utils\Contracts\Comparator;

/**
 * Has Comparator Concern
 *
 * This concern exists to provide a base implementation of
 * {@see \Smpl\Utils\Contracts\ComparesValues} with the intention of
 * avoiding boilerplate.
 *
 * @template E of mixed
 *
 * @requires \Smpl\Utils\Contracts\ComparesValues<E>
 */
trait HasComparator
{
    /**
     * @var \Smpl\Utils\Contracts\Comparator<E>|null
     */
    protected ?Comparator $comparator = null;

    /**
     * @return \Smpl\Utils\Contracts\Comparator<E>|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->comparator;
    }

    /**
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return static
     */
    public function setComparator(?Comparator $comparator = null): static
    {
        $this->comparator = $comparator;

        return $this;
    }
}