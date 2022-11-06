<?php
declare(strict_types=1);

namespace Smpl\Collections\Operations;

/**
 * @template E of mixed
 * @extends \Smpl\Collections\Operations\BaseOperation<\Smpl\Collections\Contracts\Collection<array-key, E>, string>
 */
final class JoinOperation extends BaseOperation
{
    /**
     * @var string
     */
    private string $glue;

    /**
     * @param string $glue
     */
    public function __construct(string $glue = '')
    {
        $this->glue = $glue;
    }

    /**
     * @param \Smpl\Collections\Contracts\Collection<array-key, E> $value
     *
     * @return string
     */
    public function apply(mixed $value): string
    {
        $string = '';

        foreach ($value as $element) {
            $string .= ((string)$element) . $this->glue;
        }

        if ($this->glue !== '') {
            $string = substr($string, -strlen($this->glue));
        }

        return $string;
    }
}