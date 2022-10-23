<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Utils\Contracts\Predicate;

/**
 * Chains Collection Concerns
 *
 * This concern exists as an implementation of {@see \Smpl\Collections\Contracts\ChainableCollection}
 * to simplify its usage, as most of its methods are designed to proxy other
 * methods founds in {@see \Smpl\Collections\Contracts\MutableCollection}.
 *
 * @template I of mixed
 * @template E of mixed
 * @requires \Smpl\Collections\Contracts\ChainableCollection<I, E>
 * @mixin \Smpl\Collections\Contracts\Collection<I, E>
 */
trait ChainsCollection
{
    /**
     * @param E $element
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::add()
     */
    public function push(mixed $element): static
    {
        $this->add($element);
        return $this;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::addAll()
     */
    public function pushAll(iterable $elements): static
    {
        $this->addAll($elements);
        return $this;
    }

    /**
     * @param E $element
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::remove()
     */
    public function forget(mixed $element): static
    {
        $this->remove($element);
        return $this;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::removeAll()
     */
    public function forgetAll(iterable $elements): static
    {
        $this->removeAll($elements);
        return $this;
    }

    /**
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::removeIf()
     */
    public function forgetIf(Predicate $filter): static
    {
        $this->removeIf($filter);
        return $this;
    }

    /**
     * @param iterable<E> $elements
     *
     * @return static
     *
     * @see \Smpl\Collections\Contracts\MutableCollection::retainAll()
     */
    public function keepAll(iterable $elements): static
    {
        $this->retainAll($elements);
        return $this;
    }
}