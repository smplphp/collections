<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Concerns\DecoratesImmutableCollection;
use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Comparator;
use Smpl\Collections\ImmutableCollection;

class DecoratedImmutableCollection implements Collection
{
    use DecoratesImmutableCollection;

    /**
     * @var \Smpl\Collections\ImmutableCollection
     */
    private ImmutableCollection $collection;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->collection = new ImmutableCollection($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new static($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new static($this->collection->copy($elements));
    }

    protected function delegate(): Collection
    {
        return $this->collection;
    }
}