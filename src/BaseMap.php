<?php
declare(strict_types=1);

namespace Smpl\Collections;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Hashable;
use Smpl\Collections\Contracts\Set;
use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Collections\Helpers\IterableHelper;
use Smpl\Collections\Support\MapEntry;
use Smpl\Utils\Contracts\BiFunc;
use Smpl\Utils\Contracts\Func;
use Smpl\Utils\Contracts\Predicate;
use Smpl\Utils\Contracts\ReturnsValue;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template K of array-key
 * @template V of mixed
 * @extends \Smpl\Collections\BaseCollection<K, V>
 * @implements \Smpl\Collections\Contracts\Map<K, V>
 */
abstract class BaseMap extends BaseCollection implements Contracts\Map
{
    /**
     * @param V      $value
     * @param K|null $key
     *
     * @return bool
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     *
     * @psalm-suppress ParamNameMismatch
     *
     * @noinspection   PhpParameterNameChangedDuringInheritanceInspection
     */
    public function add(mixed $value, mixed $key = null): bool
    {
        if ($key === null) {
            if ($value instanceof Hashable) {
                /** @var K $key */
                $key = $value->getHashCode();
            } else {
                throw InvalidArgumentException::noKey();
            }
        }

        $existed              = isset($this->elements[$key]);
        $this->elements[$key] = $value;

        if (! $existed) {
            $this->modifyCount(1);
        }

        return true;
    }

    /**
     * @param iterable<K, V> $values
     *
     * @return bool
     *
     * @psalm-suppress ParamNameMismatch
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @noinspection   PhpParameterNameChangedDuringInheritanceInspection
     */
    public function addAll(iterable $values): bool
    {
        /** @infection-ignore-all */
        $modified = false;

        foreach ($values as $key => $value) {
            if ($this->add($value, $key)) {
                $modified = true;
            }
        }

        return $modified;
    }

    public function forget(mixed $key): static
    {
        if ($this->has($key)) {
            unset($this->elements[$key]);
            $this->modifyCount(-1);
        }

        return $this;
    }

    public function forgetAll(iterable $keys): static
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }

        return $this;
    }

    public function forgetIf(Predicate $filter): static
    {
        foreach ($this->elements as $key => $value) {
            if ($filter->test(new MapEntry($key, $value))) {
                $this->forget($key);
            }
        }

        return $this;
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        return $this->elements[$key] ?? $default;
    }

    public function has(mixed $key): bool
    {
        return isset($this->elements[$key]);
    }

    public function hasAll(iterable $keys): bool
    {
        foreach ($keys as $key) {
            if (! $this->has($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param iterable<array-key, K> $keys
     *
     * @return static
     *
     * @psalm-suppress UnusedForeachValue
     */
    public function keepAll(iterable $keys): static
    {
        $keys = IterableHelper::iterableToArray($keys);

        foreach ($this->elements as $key => $value) {
            if (! in_array($key, $keys, true)) {
                $this->forget($key);
            }
        }

        return $this;
    }

    public function keys(): Set
    {
        return new ImmutableSet(array_keys($this->elements));
    }

    public function put(mixed $key, mixed $value): static
    {
        $existed              = isset($this->elements[$key]);
        $this->elements[$key] = $value;

        if (! $existed) {
            $this->modifyCount(1);
        }

        return $this;
    }

    public function putAll(iterable $values): static
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value);
        }

        return $this;
    }

    public function replace(mixed $key, mixed $value): bool
    {
        if (! $this->has($key)) {
            return false;
        }

        $this->put($key, $value);

        return true;
    }

    public function replaceIf(mixed $key, mixed $value, Predicate $predicate): bool
    {
        if (! $this->has($key)) {
            return false;
        }

        if (! $predicate->test($this->elements[$key])) {
            return false;
        }

        $this->put($key, $value);

        return true;
    }

    /**
     * @param K                                     $key
     * @param \Smpl\Utils\Contracts\ReturnsValue<V> $valueRetriever
     *
     * @return bool
     */
    public function replaceWith(mixed $key, ReturnsValue $valueRetriever): bool
    {
        if (! $this->has($key)) {
            return false;
        }

        /**
         * @psalm-suppress MixedAssignment
         */
        if ($valueRetriever instanceof Supplier) {
            $value = $valueRetriever->get();
        } else if ($valueRetriever instanceof Func) {
            $value = $valueRetriever->apply($this->get($key));
        } else if ($valueRetriever instanceof BiFunc) {
            $value = $valueRetriever->apply($key, $this->get($key));
        }

        if (! isset($value)) {
            throw InvalidArgumentException::noReplacementFromRetriever($key);
        }

        /** @var V $value */

        return $this->replace($key, $value);
    }

    /**
     * @return \Smpl\Collections\ImmutableCollection<V>
     */
    public function values(): Collection
    {
        return new ImmutableCollection(array_values($this->toArray()));
    }

    /**
     * @return array<K, V>
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function toArray(): array
    {
        return $this->elements;
    }
}