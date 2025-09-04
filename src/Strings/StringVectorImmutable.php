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
final readonly class StringVectorImmutable implements StringVectorInterface
{
    use StringVectorTrait;
}