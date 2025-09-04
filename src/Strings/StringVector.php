<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings;

use Charcoal\Contracts\Vectors\StringVectorInterface;
use Charcoal\Vectors\Strings\Traits\StringVectorTrait;

/**
 * Represents a vector of strings with utility methods for appending,
 * filtering unique values, and accessing the collection.
 */
final class StringVector implements StringVectorInterface
{
    use StringVectorTrait;

    /**
     * @return StringVectorImmutable
     */
    public function toImmutable(): StringVectorImmutable
    {
        return new StringVectorImmutable(...$this->strings);
    }

    /**
     * @param string ...$values
     * @return $this
     */
    public function append(string ...$values): self
    {
        foreach ($values as $value) {
            $value = trim($value);
            if ($value !== "") {
                $this->strings[] = $value;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function filterUnique(): self
    {
        $this->strings = array_values(array_unique($this->strings, SORT_STRING));
        return $this;
    }
}