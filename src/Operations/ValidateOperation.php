<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

use Smpl\Collections\Support\Collections;
use Smpl\Utils\Contracts\Func;
use Smpl\Utils\Contracts\Predicate;

/**
 * @template E of mixed
 * @template T of \Throwable
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, \Smpl\Collections\Contracts\Collection<array-key, E>>
 */
final class ValidateOperation extends BaseOperation
{
    /**
     * @var \Smpl\Utils\Contracts\Predicate<E>
     */
    private Predicate $filter;

    /**
     * @var \Smpl\Utils\Contracts\Func<\Smpl\Collections\Contracts\Collection<array-key, E>, T>
     */
    private Func $throwable;

    /**
     * @param \Smpl\Utils\Contracts\Predicate<E>                                                  $filter
     * @param \Smpl\Utils\Contracts\Func<\Smpl\Collections\Contracts\Collection<array-key, E>, T> $throwable
     */
    public function __construct(Predicate $filter, Func $throwable)
    {
        $this->filter    = $filter;
        $this->throwable = $throwable;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return \Smpl\Collections\Contracts\Collection<array-key, E>
     */
    public function apply(mixed $value): mixed
    {
        $elements = [];

        foreach ($value as $element) {
            if (! $this->filter->test($element)) {
                $elements[] = $element;
            }
        }

        if (! empty($elements)) {
            throw $this->throwable->apply(Collections::immutableCollection($elements));
        }

        return $value;
    }
}