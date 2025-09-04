<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Tests;

use Charcoal\Vectors\Tests\Fixtures\TestEnum;
use Charcoal\Vectors\Tests\Fixtures\TestEnumBacked;
use Charcoal\Vectors\Tests\Fixtures\TestEnumBag;
use PHPUnit\Framework\TestCase;

final class EnumVectorTest extends TestCase
{
    public function testEnumVectorCaseNamesValuesIndexingSortingAndUniqueness(): void
    {
        $vec = new TestEnumBag(TestEnum::One, TestEnum::Two, TestEnum::One, TestEnum::Three, TestEnum::Two);

        // Sorting on (default): names are alphabetically sorted per enum class
        $sortedNamesByClass = $vec->names(TestEnum::class);
        $this->assertSame(["One", "Three", "Two"], $sortedNamesByClass);

        // Values for pure UnitEnum are nulls, count matches names
        $sortedValuesByClass = $vec->values(TestEnum::class);
        $this->assertSame([null, null, null], $sortedValuesByClass);

        // Indexing by integer 0 should reference the first (and only) class
        $this->assertSame(["One", "Three", "Two"], $vec->names(0));
        $this->assertSame([null, null, null], $vec->values(0));

        // Invalid indices return null
        $this->assertNull($vec->names(-1));
        $this->assertNull($vec->values(-1));
        $this->assertNull($vec->names(""));
        $this->assertNull($vec->values("NonExistent\\Enum"));

        // Disable sorting to check stability of insertion order after uniqueness
        $vec->setSorting(false)->unique();
        // After filterUnique, first occurrences kept in original encounter order: One, Two, Three
        $this->assertSame(["One", "Two", "Three"], $vec->names(0));
        $this->assertSame([null, null, null], $vec->values(0));
    }

    public function testEnumBackedVectorCaseNamesValuesIndexingSortingAndUniqueness(): void
    {
        $vec = new TestEnumBag(TestEnumBacked::One, TestEnumBacked::Two, TestEnumBacked::One,
            TestEnumBacked::Three, TestEnumBacked::Two);

        // Sorting on (default): names are alphabetically sorted per enum class
        $sortedNamesByClass = $vec->names(TestEnumBacked::class);
        $this->assertSame(["One", "Three", "Two"], $sortedNamesByClass);

        // Values for pure UnitEnum are nulls, count matches names
        $sortedValuesByClass = $vec->values(TestEnumBacked::class);
        $this->assertSame(["one", "three", "two"], $sortedValuesByClass);

        // Indexing by integer 0 should reference the first (and only) class
        $this->assertSame(["One", "Three", "Two"], $vec->names(0));
        $this->assertSame(["one", "three", "two"], $vec->values(0));

        // Invalid indices return null
        $this->assertNull($vec->names(-1));
        $this->assertNull($vec->values(-1));
        $this->assertNull($vec->names(""));
        $this->assertNull($vec->values("NonExistent\\Enum"));

        // Disable sorting to check stability of insertion order after uniqueness
        $vec->setSorting(false)->unique();
        // After filterUnique, first occurrences kept in original encounter order: One, Two, Three
        $this->assertSame(["One", "Two", "Three"], $vec->names(0));
        $this->assertSame(["one", "two", "three"], $vec->values(0));
    }
}