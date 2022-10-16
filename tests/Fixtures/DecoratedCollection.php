<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Collection;
use Smpl\Collections\Concerns\DecoratesMutableCollection;
use Smpl\Collections\Contracts\Comparator;
use Smpl\Collections\Contracts\MutableCollection;

class DecoratedCollection implements MutableCollection
{
    use DecoratesMutableCollection;

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

    protected function delegate(): MutableCollection
    {
        return $this->collection;
    }
}