<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Contracts\Deque as DequeContract;
use Smpl\Collections\Decorators\DecoratesDeque;
use Smpl\Collections\Deque;
use Smpl\Utils\Contracts\Comparator;

class DecoratedDeque implements DequeContract
{
    use DecoratesDeque;

    /**
     * @var \Smpl\Collections\Contracts\Deque
     */
    private DequeContract $queue;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->queue = new Deque($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new self($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new self($elements ?? $this->delegate(), $this->delegate()->getComparator());
    }

    protected function delegate(): DequeContract
    {
        return $this->queue;
    }
}