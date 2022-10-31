<?php
declare(strict_types=1);

namespace Smpl\Collections\Tests\Fixtures;

use Smpl\Collections\Contracts\Queue as QueueContract;
use Smpl\Collections\Decorators\DecoratesQueue;
use Smpl\Collections\Queue;
use Smpl\Utils\Contracts\Comparator;

class DecoratedQueue implements QueueContract
{
    use DecoratesQueue;

    /**
     * @var \Smpl\Collections\Contracts\Queue
     */
    private QueueContract $queue;

    public function __construct(iterable $elements = [], ?Comparator $comparator = null)
    {
        $this->queue = new Queue($elements, $comparator);
    }

    public static function of(...$elements): static
    {
        return new self($elements);
    }

    public function copy(iterable $elements = null): static
    {
        return new self($elements ?? $this->delegate(), $this->delegate()->getComparator());
    }

    protected function delegate(): QueueContract
    {
        return $this->queue;
    }
}