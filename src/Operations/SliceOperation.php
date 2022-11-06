<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Contracts\Collection;
use Smpl\Collections\Contracts\Sequence;
use Smpl\Collections\Exceptions\OutOfRangeException;
use Smpl\Collections\Support\Collections;
use Smpl\Utils\Support\Range;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class SliceOperation extends BaseOperation
{
    /**
     * @var int|null
     */
    private ?int $length;

    /**
     * @var int
     */
    private int $offset;

    /**
     * @param int<0, max>      $offset
     * @param int<0, max>|null $length
     */
    public function __construct(int $offset, ?int $length = null)
    {
        $this->offset = $offset;
        $this->length = $length;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): Collection
    {
        $range = Range::for($value);

        if ($range->doesNotCover($this->offset)) {
            throw OutOfRangeException::fromRange($this->offset, $range);
        }

        if ($this->length !== null && $range->doesNotCover($this->offset + $this->length)) {
            throw OutOfRangeException::fromRange($this->offset + $this->length, $range);
        }

        if ($value instanceof Sequence) {
            /** @psalm-suppress ArgumentTypeCoercion */
            return $value->subset($this->offset, $this->length);
        }

        $index    = 0;
        $length   = 0;
        $elements = [];

        foreach ($value as $element) {
            if ($index >= $this->offset) {
                $elements[] = $element;

                if ($this->length !== null) {
                    $length++;

                    if ($length === $this->length) {
                        break;
                    }
                }

            }

            $index++;
        }

        return Collections::from($value, $elements);
    }
}