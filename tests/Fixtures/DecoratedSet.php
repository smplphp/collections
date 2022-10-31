<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Contracts\Set as SetContract;
use Smpl\Collections\Decorators\DecoratesSet;
use Smpl\Collections\Set;
use Smpl\Utils\Contracts\Comparator;

class DecoratedSet implements SetContract
{
    use DecoratesSet;

    /**
     * @var \Smpl\Collections\Set
     */
    private Set $set;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->set = new Set($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new static($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new static($this->set->copy($elements));
    }

    protected function delegate(): SetContract
    {
        return $this->set;
    }
}