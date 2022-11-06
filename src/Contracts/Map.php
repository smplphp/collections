<?php

namespace Smpl\Collections\Contracts;

use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Contracts\ReturnsValue;

/**
 * Map Contract
 *
 * This contract represents a map, a type of collection that associates keys
 * with values, rather than has elements with an index. This collection is
 * more like an associative array.
 *
 * @template K of array-key
 * @template V of mixed
 * @extends \Smpl\Collections\Contracts\Collection<K, V>
 */
interface Map extends Collection
{
    /**
     * Ensure that this collection contains the provided key => value pair.
     *
     * This method will ensure that the collection contains the provided element,
     * returning true if the collection was modified. In the case of the
     * implementor not allowing duplicates, this method will return false.
     *
     * If the element provided cannot be added to the collection for a reason
     * other than it being duplicate, such as it being null, the implementor
     * must throw a {@see \Smpl\Collections\Exceptions\InvalidArgumentException}
     * exception.
     *
     * If $key is null, and $value implements {@see \Smpl\Collections\Contracts\Hashable},
     * {@see \Smpl\Collections\Contracts\Hashable::getHashCode()} will be used
     *  as $key, otherwise an exception will be thrown.
     *
     * @param V      $value
     * @param K|null $key
     *
     * @return bool
     *
     * @psalm-suppress ParamNameMismatch
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function add(mixed $value, mixed $key = null): bool;

    /**
     * Ensure that this collection contains all the provided elements.
     *
     * This method will function exactly like
     * {@see \Smpl\Collections\Contracts\Collection::add()} except that it
     * deals with multiple elements, rather than just one.
     *
     * Because of this, it is possible for this method to return true, even if
     * only one of the provided elements are actually added to this collection.
     *
     * This method must also throw a {@see \Smpl\Collections\Exceptions\InvalidArgumentException}
     * if any of the provided elements cannot be added to the collection for
     * reasons other than being a duplicate.
     *
     * The provided values should come as key => value pairs.
     *
     * @param iterable<K, V> $values
     *
     * @return bool
     *
     * @psalm-suppress ParamNameMismatch
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @noinspection   PhpParameterNameChangedDuringInheritanceInspection
     */
    public function addAll(iterable $values): bool;

    /**
     * Remove the key => value pair from this collection, for the provided key.
     *
     * This method will remove a key => value pair with the provided key from
     * the collection, returning true if the collection was modified, false otherwise.
     *
     * Unlike its {@see \Smpl\Collections\Contracts\Collection::remove()}
     * counterpart, this method must not use a comparator, and should instead
     * use an identicality check.
     *
     * This method is the key equivalent of
     * {@see \Smpl\Collections\Contracts\Collection::remove()}.
     *
     * @param K $key
     *
     * @return static
     */
    public function forget(mixed $key): static;

    /**
     * Remove all key => value pairs with the provided keys from this collection.
     *
     * This method will remove all key => value pairs from this collection that
     * have keys in the provided $keys, returning true if the collection
     * was modified, false otherwise, functioning like
     * {@see \Smpl\Collections\Contracts\Map::forget()}, but for
     * multiple elements.
     *
     * Because of this, it is possible for this method to return true, even if
     * only one of the provided elements were actually removed from this collection.
     *
     * Unlike its {@see \Smpl\Collections\Contracts\Collection::removeAll()}
     * counterpart, this method must not use a comparator, and should instead
     * use an identicality check.
     *
     * This method is the key equivalent of
     * {@see \Smpl\Collections\Contracts\Collection::removeAll()}.
     *
     * @param iterable<K> $keys
     *
     * @return static
     */
    public function forgetAll(iterable $keys): static;

    /**
     * Remove all key => value pairs from this collection that pass the provided filter.
     *
     * This method will remove all key => value pairs from this collection that pass
     * the provided filter, returning true if this collection was modified,
     * false otherwise.
     *
     * This method is the key equivalent of
     * {@see \Smpl\Collections\Contracts\Collection::removeIf()}.
     *
     * @param \Smpl\Utils\Contracts\Predicate<\Smpl\Collections\Contracts\MapEntry<K, V>> $filter
     *
     * @return static
     */
    public function forgetIf(Predicate $filter): static;

    /**
     * Get a value by its key.
     *
     * This method will retrieve a value by its key, if present. If the key
     * does not exist in the collection, the value of $default will be
     * returned instead.
     *
     * @param K      $key
     * @param V|null $default
     *
     * @return V|null
     */
    public function get(mixed $key, mixed $default = null): mixed;

    /**
     * Check if this collection contains the provided key.
     *
     * This method should return true if the provided key exists within
     * the collection. Unlike its {@see \Smpl\Collections\Contracts\Collection::contains()}
     * counterpart, no comparator should be used, and checks for identicality
     * should be used instead.
     *
     * This method is the key equivalent of
     * {@see \Smpl\Collections\Contracts\Collection::contains()}.
     *
     * @param K $key
     *
     * @return bool
     */
    public function has(mixed $key): bool;

    /**
     * Check if this collection contains all provided keys.
     *
     * This method should return true if all the provided keys exist within
     * the collection. Unlike its {@see \Smpl\Collections\Contracts\Collection::containsAll()}
     * counterpart, no comparator should be used, and checks for identicality
     * should be used instead.
     *
     * Should any of the provided keys not be found in the collection,
     * this method should return false.
     *
     * This method is the key equivalent of
     * {@see \Smpl\Collections\Contracts\Collection::containsAll()}.
     *
     * @param iterable<K> $keys
     *
     * @return bool
     */
    public function hasAll(iterable $keys): bool;

    /**
     * Remove all key => value pairs that do not have keys in the provided list.
     *
     * This method will function as the opposite of
     * {@see \Smpl\Collections\Contracts\Map::forgetAll()}, removing
     * all but the pairs with keys provided by $keys. This method will return true
     * if the collection was modified, false otherwise.
     *
     * It is possible for this method to return true, even if only one pair is
     * removed from this collection.
     *
     * If the provided keys contain keys not present in the collection,
     * they will not be added.
     *
     * The exact method for determining whether a pair should be removed
     * will depend entirely on the implementation.
     *
     * This method is the key equivalent of
     * {@see \Smpl\Collections\Contracts\Collection::retainAll()}.
     * 
     * @param iterable<array-key, K> $keys
     *
     * @return static
     */
    public function keepAll(iterable $keys): static;

    /**
     * Get all the keys from this collection.
     *
     * This method will return a collection of keys currently present in this
     * collection. A {@see \Smpl\Collections\Contracts\Set} is used because
     * keys must be unique, and sets enforce this.
     *
     * @return \Smpl\Collections\Contracts\Set<K>
     */
    public function keys(): Set;

    /**
     * @param K $key
     * @param V $value
     *
     * @return static
     */
    public function put(mixed $key, mixed $value): static;

    /**
     * @param iterable<K, V> $values
     *
     * @return static
     */
    public function putAll(iterable $values): static;

    /**
     * @param K $key
     * @param V $value
     *
     * @return bool
     */
    public function replace(mixed $key, mixed $value): bool;

    /**
     * @param K                                  $key
     * @param V                                  $value
     * @param \Smpl\Utils\Contracts\Predicate<V> $predicate
     *
     * @return bool
     */
    public function replaceIf(mixed $key, mixed $value, Predicate $predicate): bool;

    /**
     * @param K                                     $key
     * @param \Smpl\Utils\Contracts\ReturnsValue<V> $valueRetriever
     *
     * @return bool
     */
    public function replaceWith(mixed $key, ReturnsValue $valueRetriever): bool;

    /**
     * @return \Smpl\Collections\Contracts\Collection<int, V>
     */
    public function values(): Collection;
}