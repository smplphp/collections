<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Contracts\Sequence as SequenceContract;
use Smpl\Collections\Decorators\DecoratesSequence;
use Smpl\Collections\Sequence;
use Smpl\Utils\Contracts\Comparator;

class DecoratedSequence implements SequenceContract
{
    use DecoratesSequence;

    /**
     * @var \Smpl\Collections\Sequence
     */
    private Sequence $sequence;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->sequence = new Sequence($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new static($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new static($this->sequence->copy($elements));
    }

    protected function delegate(): SequenceContract
    {
        return $this->sequence;
    }
}