<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings\Traits;

/**
 * Provides immutable operations for managing a collection of strings.
 * This trait is intended to be used with classes that handle an internal array of strings.
 */
trait StringVectorImmutableTrait
{
    /**
     * @param string $glue
     * @return string
     */
    public function join(string $glue): string
    {
        if (strlen($glue) !== 1) {
            throw new \InvalidArgumentException("Invalid glue byte");
        }

        return implode($glue, $this->strings);
    }

    /**
     * @return \Traversable<int,string>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->strings);
    }

    /**
     * @return array<int,string>
     */
    public function getArray(): array
    {
        return $this->strings;
    }
}