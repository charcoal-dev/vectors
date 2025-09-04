<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings\Traits;

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
     * @return int
     */
    public function count(): int
    {
        return count($this->strings);
    }
}