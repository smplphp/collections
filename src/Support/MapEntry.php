<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

use Smpl\Collections\Contracts\MapEntry as MapEntryContract;

/**
 * @template K of array-key
 * @template V of mixed
 * @implements \Smpl\Collections\Contracts\MapEntry<K, V>
 */
final class MapEntry implements MapEntryContract
{
    /**
     * @var K
     */
    private mixed $key;

    /**
     * @var V
     */
    private mixed $value;

    /**
     * @param K $key
     * @param V $value
     */
    public function __construct(mixed $key, mixed $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return K
     */
    public function getKey(): mixed
    {
        return $this->key;
    }

    /**
     * @return V
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}