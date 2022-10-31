<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Contracts\Stack as StackContract;
use Smpl\Collections\Decorators\DecoratesStack;
use Smpl\Collections\Stack;
use Smpl\Utils\Contracts\Comparator;

class DecoratedStack implements StackContract
{
    use DecoratesStack;

    /**
     * @var \Smpl\Collections\Contracts\Stack
     */
    private StackContract $stack;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->stack = new Stack($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new self($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new self($elements ?? $this->delegate(), $this->delegate()->getComparator());
    }

    protected function delegate(): StackContract
    {
        return $this->stack;
    }
}