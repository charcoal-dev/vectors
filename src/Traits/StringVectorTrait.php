<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings;

/**
 * Provides functionality for managing a collection of strings with added capabilities
 * such as appending values, ensuring uniqueness, and retrieving the collection in
 * various forms.
 */
trait StringVectorTrait
{
    protected array $strings;

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