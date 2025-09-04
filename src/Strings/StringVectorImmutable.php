<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings;

use Charcoal\Contracts\Vectors\StringVectorInterface;

/**
 * Represents an immutable collection of strings.
 */
final class StringVectorImmutable implements StringVectorInterface
{
    private array $strings;

    /**
     * @param string ...$values
     */
    public function __construct(string ...$values)
    {
        $this->strings = $values;
    }

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
     * @return int
     */
    public function count(): int
    {
        return count($this->strings);
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