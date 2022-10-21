<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Collection;
use Smpl\Collections\Concerns\DecoratesCollection;
use Smpl\Collections\Contracts\Collection as CollectionContract;
use Smpl\Utils\Contracts\Comparator;

class DecoratedCollection implements CollectionContract
{
    use DecoratesCollection;

    /**
     * @var \Smpl\Collections\Collection
     */
    private Collection $collection;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->collection = new Collection($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new static($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new static($this->collection->copy($elements));
    }

    protected function delegate(): CollectionContract
    {
        return $this->collection;
    }
}