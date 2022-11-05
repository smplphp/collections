<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Exceptions\UnsupportedOperationException;
use Smpl\Utils\Contracts\Comparator;
use Smpl\Utils\Contracts\Predicate;

/**
 * Base Immutable Collection
 *
 * This class forms the base for immutable collections, preventing the need to duplicate
 * code and method definitions where inheritance will suffice.
 *
 * This class throws {@see \Smpl\Collections\Exceptions\UnsupportedOperationException}
 * exceptions for all methods that would modify this collection.
 *
 * @template       E of mixed
 * @extends \Smpl\Collections\BaseCollection<int, E>
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
abstract class BaseImmutableCollection extends BaseCollection
{
    /**
     * @param iterable<E>|null                         $elements
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(iterable $elements = null, ?Comparator $comparator = null)
    {
        if ($elements !== null) {
            foreach ($elements as $element) {
                $this->elements[] = $element;
                $this->count++;
            }
        }

        $this->comparator = $comparator;
    }

    /**
     * @param E $element
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function add(mixed $element): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function addAll(iterable $elements): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function clear(): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @param E $element
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function remove(mixed $element): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function removeAll(iterable $elements): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @param \Smpl\Utils\Contracts\Predicate<E> $filter
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function removeIf(Predicate $filter): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @param iterable<E> $elements
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function retainAll(iterable $elements): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }

    /**
     * @param \Smpl\Utils\Contracts\Comparator<E>|null $comparator
     *
     * @return never
     *
     * @throws \Smpl\Collections\Exceptions\UnsupportedOperationException
     */
    public function setComparator(?Comparator $comparator = null): never
    {
        throw UnsupportedOperationException::mutable(__CLASS__, __METHOD__);
    }
}