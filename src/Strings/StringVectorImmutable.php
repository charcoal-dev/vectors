<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings;

use Charcoal\Contracts\Vectors\StringVectorInterface;
use Charcoal\Vectors\Strings\Traits\StringVectorImmutableTrait;

/**
 * Represents an immutable collection of strings.
 */
final readonly class StringVectorImmutable implements StringVectorInterface
{
    use StringVectorImmutableTrait;

    private array $strings;
    private int $length;

    /**
     * @param string ...$strings
     */
    public function __construct(string ...$strings)
    {
        $this->strings = $strings;
        $this->length = count($strings);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->length;
    }
}