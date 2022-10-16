<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\BaseCollection;
use Smpl\Collections\Concerns\ChainsCollection;
use Smpl\Collections\Concerns\NewCollectionOfElements;
use Smpl\Collections\Contracts\ChainableCollection;

class ChainedCollection extends BaseCollection implements ChainableCollection
{
    use ChainsCollection,
        NewCollectionOfElements;

    public function copy(iterable $elements = null): static
    {
        return new static($elements ?? $this->elements);
    }
}