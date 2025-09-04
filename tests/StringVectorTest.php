<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Tests;

use Charcoal\Vectors\Strings\StringVector;
use Charcoal\Vectors\Strings\StringVectorImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the StringVector class.
 */
final class StringVectorTest extends TestCase
{
    public function testImmutableGetArrayForeachOrderCountAndJoin(): void
    {
        $imm = new StringVectorImmutable("A", "b", "C");

        // getArray reflects constructor input
        $this->assertSame(["A", "b", "C"], $imm->getArray());

        // foreach iteration preserves order
        $iterated = [];
        foreach ($imm as $v) {
            $iterated[] = $v;
        }
        $this->assertSame(["A", "b", "C"], $iterated);

        // count matches number of elements
        $this->assertSame(3, $imm->count());

        // join accepts single-byte glue
        $this->assertSame("A,b,C", $imm->join(","));
    }

    public function testAppendTrimsSkipsAndChainsWithBinaryAndWhitespace(): void
    {
        $vec = new StringVector();
        $ret = $vec->append(" a ", "", " \t ", "\n", "\r", "\0", "B", " c");
        $this->assertSame($vec, $ret);
        $this->assertSame(["a", "B", "c"], $vec->getArray());
    }

    public function testAppendNoArgsLeavesVectorUnchanged(): void
    {
        $vec = new StringVector("x");
        $before = $vec->getArray();
        $vec->append();
        $this->assertSame($before, $vec->getArray());
        $this->assertSame(1, $vec->count());
    }

    public function testFilterUniqueCaseSensitivePreservesFirstOccurrenceOrder(): void
    {
        $vec = new StringVector("A", "a", "A", "b", "B", "b", "a", "A");
        $vec->filterUnique();
        $this->assertSame(["A", "a", "b", "B"], $vec->getArray());
    }

    public function testFilterUniqueIdempotentOverMultipleCalls(): void
    {
        $vec = new StringVector("x", "x", "y", "y", "y");
        $vec->filterUnique();
        $this->assertSame(["x", "y"], $vec->getArray());
        $vec->filterUnique();
        $this->assertSame(["x", "y"], $vec->getArray());
    }

    public function testJoinWithSingleByteGlueIncludingControlAndNul(): void
    {
        $vec = new StringVector();
        $vec->append("A", "b", "C");
        $this->assertSame("A,b,C", $vec->join(","));
        $this->assertSame("A\tb\tC", $vec->join("\t"));
        $this->assertSame("A\0b\0C", $vec->join("\0"));
    }

    public function testJoinThrowsOnInvalidGlueLength(): void
    {
        $vec = new StringVector("a", "b");
        $this->expectException(\InvalidArgumentException::class);
        $vec->join("");
    }

    public function testJoinThrowsOnMultiByteGlue(): void
    {
        $vec = new StringVector("a", "b");
        $this->expectException(\InvalidArgumentException::class);
        $vec->join("::");
    }

    public function testToImmutableReturnsSnapshotNotAffectedByFurtherMutations(): void
    {
        $vec = new StringVector();
        $vec->append("a", "b", "a");
        $imm = $vec->toImmutable();
        $this->assertSame(["a", "b", "a"], $imm->getArray());

        $vec->append("c")->filterUnique();
        // Immutable snapshot remains as it was at creation time
        $this->assertSame(["a", "b", "a"], $imm->getArray());
        // Mutable vector now deduped
        $this->assertSame(["a", "b", "c"], $vec->getArray());
    }

    public function testIteratorTraversesAllValuesInOrder(): void
    {
        $vec = new StringVector();
        $vec->append("x", "y", "z");
        $iterated = [];
        foreach ($vec as $v) {
            $iterated[] = $v;
        }
        $this->assertSame(["x", "y", "z"], $iterated);
    }

    public function testCountReflectsInternalSizeAccurately(): void
    {
        $vec = new StringVector();
        $this->assertSame(0, $vec->count());
        $vec->append("a", "b", "  ", "c");
        $this->assertSame(3, $vec->count());
        $vec->filterUnique();
        $this->assertSame(3, $vec->count());
    }

    public function testForeachIteratesInSameOrderAsGetArray(): void
    {
        $vec = new StringVector();
        $vec->append("a", "b", "c");

        $iterated = [];
        foreach ($vec as $value) {
            $iterated[] = $value;
        }

        $this->assertSame($vec->getArray(), $iterated);
    }
}