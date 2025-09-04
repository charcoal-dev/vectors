<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Enums;

use Charcoal\Contracts\Vectors\VectorInterface;

/**
 * Abstract class representing a collection of enumerations, providing
 * methods to manage and manipulate the collection.
 * @template T of \UnitEnum
 * @template-implements VectorInterface<T>
 */
abstract class AbstractEnumVector implements VectorInterface
{
    protected bool $sorting = true;
    protected array $cases;

    /**
     * @param \UnitEnum ...$values
     */
    protected function __construct(\UnitEnum ...$values)
    {
        $this->cases = $values;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->cases);
    }

    /**
     * @return \Traversable<int,T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->cases);
    }

    /**
     * @return array<int,T>
     */
    public function getArray(): array
    {
        return $this->cases;
    }

    /**
     * @return $this
     */
    public function filterUnique(): static
    {
        /** @var \UnitEnum[] $values */
        $values = $this->cases;
        $this->cases = self::filterUniqueFromSet(...$values);
        return $this;
    }

    /**
     * @param int|class-string $index
     * @param null|array<string, array<string, int|string|null>> $classmap
     * @return null|array<int|string|null>
     * @api
     */
    protected function getCaseValues(int|string $index, ?array $classmap = null): ?array
    {
        $cases = $this->getCaseMap($index, $classmap);
        if (!is_array($cases)) {
            return null;
        }

        if ($this->sorting && $cases) {
            sort($cases, is_int($cases[0]) ? SORT_NUMERIC : SORT_STRING);
        }

        return array_values($cases);
    }

    /**
     * @param int|class-string $index
     * @param null|array<string, array<string, int|string|null>> $classmap
     * @return null|string[]
     * @api
     */
    protected function getCaseNames(int|string $index, ?array $classmap = null): ?array
    {
        $cases = $this->getCaseMap($index, $classmap);
        return is_array($cases) ? array_keys($cases) : null;
    }

    /**
     * @param int|class-string $index
     * @param null|array<string, array<string, int|string|null>> $classmap
     * @return null|array<string, int|string|null>
     */
    protected function getCaseMap(int|string $index, ?array $classmap = null): ?array
    {
        if ((is_int($index) && $index < 0) || $index === "") {
            return null;
        }

        $classmap ??= $this->createEnumsClassmap();
        return is_string($index) ? $classmap[$index] ?? null :
            array_values($classmap)[$index] ?? null;
    }

    /**
     * @return array<string, array<string, int|string|null>>
     */
    protected function createEnumsClassmap(): array
    {
        $classes = [];
        foreach ($this->cases as $value) {
            $classes[$value::class] ??= [];
            $classes[$value::class][$value->name] = $value instanceof \BackedEnum ? $value->value : null;
        }

        if ($this->sorting) {
            ksort($classes);
            array_walk($classes, function (&$value) {
                ksort($value);
            });
        }

        return $classes;
    }

    /**
     * @param \UnitEnum ...$enums
     * @return array
     */
    private static function filterUniqueFromSet(\UnitEnum ...$enums): array
    {
        if (!$enums) {
            return [];
        }

        $unique = [];
        foreach ($enums as $case) {
            $key = $case::class . "::" . $case->name;
            $unique[$key] ??= $case;
        }

        return array_values($unique);
    }
}