<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors;

use Charcoal\Contracts\Vectors\VectorInterface;

/**
 * A vector-like structure that stores exceptions.
 * @implements VectorInterface<\Throwable>
 */
final class ExceptionVector implements VectorInterface
{
    private array $vector;

    /**
     * @param \Throwable ...$exceptions
     */
    public function __construct(\Throwable ...$exceptions)
    {
        $this->vector = $exceptions;
    }

    /**
     * @param \Throwable $exception
     * @return $this
     */
    public function append(\Throwable $exception): self
    {
        $this->vector[] = $exception;
        return $this;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->vector);
    }

    /**
     * @return \Traversable<int,\Throwable>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->vector);
    }

    /**
     * @return array<int,\Throwable>
     */
    public function getArray(): array
    {
        return $this->vector;
    }
}