<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Tests\Fixtures;

use Charcoal\Vectors\Enums\AbstractEnumVector;

/**
 * @template-extends AbstractEnumVector<TestEnum>
 */
class TestEnumBag extends AbstractEnumVector
{
    public function __construct(\UnitEnum ...$values)
    {
        parent::__construct(...$values);
    }

    public function setSorting(bool $sorting): static
    {
        $this->sorting = $sorting;
        return $this;
    }

    public function unique(): static
    {
        return $this->filterUnique();
    }

    public function names(int|string $index, ?array $classmap = null): ?array
    {
        return $this->getCaseNames($index, $classmap);
    }

    public function values(int|string $index, ?array $classmap = null): ?array
    {
        return $this->getCaseValues($index, $classmap);
    }

    public function map(int|string $index, ?array $classmap = null): ?array
    {
        return $this->getCaseMap($index, $classmap);
    }
}