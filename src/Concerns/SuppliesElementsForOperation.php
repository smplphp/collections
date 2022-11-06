<?php
declare(strict_types=1);

namespace Smpl\Collections\Concerns;

use Smpl\Collections\Exceptions\InvalidArgumentException;
use Smpl\Utils\Contracts\Supplier;

/**
 * @template E of mixed
 */
trait SuppliesElementsForOperation
{
    /**
     * @var \Smpl\Utils\Contracts\Supplier<\Smpl\Collections\Contracts\Collection<array-key, E>>|iterable<E>
     * @noinspection PhpDocFieldTypeMismatchInspection
     */
    private Supplier|iterable $elementSupplier;

    /**
     * @param \Smpl\Utils\Contracts\Supplier<\Smpl\Collections\Contracts\Collection<array-key, E>>|iterable<E> $elementSupplier
     *
     * @noinspection PhpDocSignatureInspection
     */
    protected function setElementSupplier(Supplier|iterable $elementSupplier): void
    {
        $this->elementSupplier = $elementSupplier;
    }

    /**
     * @return iterable<E>
     *
     * @throws \Smpl\Collections\Exceptions\InvalidArgumentException
     */
    protected function getElements(): iterable
    {
        $elements = $this->elementSupplier;

        if ($elements instanceof Supplier) {
            $elements = $elements->get();
        }

        if (! is_iterable($elements)) {
            throw InvalidArgumentException::invalidElementSupplier(static::class);
        }

        return $elements;
    }
}