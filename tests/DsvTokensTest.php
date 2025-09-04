<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Tests;

use Charcoal\Vectors\Support\DsvTokens;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the DsvString class.
 */
final class DsvTokensTest extends TestCase
{
    public function testAddTrimsLowercasesAndDedupesByDefault(): void
    {
        $tokens = new DsvTokens(); // default: changeCase=true, uniqueTokensOnly=true

        $returned = $tokens->add(" A ", "a", "", "B", "b", "\n");

        $this->assertSame($tokens, $returned);
        $this->assertSame(["a", "b"], $tokens->getArray());
    }

    public function testAddKeepsOriginalCaseWhenChangeCaseFalseAndDedupesCaseInsensitively(): void
    {
        $tokens = new DsvTokens(changeCase: false, uniqueTokensOnly: true);

        $tokens->add(" A ", "a", "B", "b", "b");

        $this->assertSame(["A", "B"], $tokens->getArray());
    }

    public function testAddDoesNotDeduplicateWhenUniqueTokensOnlyFalse(): void
    {
        $tokens = new DsvTokens(changeCase: true, uniqueTokensOnly: false);

        $tokens->add("x", "X", "x");

        $this->assertSame(["x", "x", "x"], $tokens->getArray());
    }

    public function testAddSkipsPureWhitespaceAndControlChars(): void
    {
        $tokens = new DsvTokens();

        $tokens->add(" \t ", "\n", "\r", " a ");

        $this->assertSame(["a"], $tokens->getArray());
    }

    public function testDeleteReturnsFalseOnEmptyOrWhitespace(): void
    {
        $tokens = new DsvTokens();
        $tokens->add("a");

        $this->assertFalse($tokens->delete(""));
        $this->assertFalse($tokens->delete("   "));
        $this->assertSame(["a"], $tokens->getArray());
    }

    public function testDeleteReturnsFalseWhenTokenNotFound(): void
    {
        $tokens = new DsvTokens();
        $tokens->add("a", "b");

        $this->assertFalse($tokens->delete("z"));
        $this->assertSame(["a", "b"], $tokens->getArray());
    }

    public function testDeleteRemovesExactMatchOnlyWhenChangeCaseFalse(): void
    {
        $tokens = new DsvTokens(changeCase: false, uniqueTokensOnly: false);
        $tokens->add("Foo", "foo", "FOO", "Bar");

        $deleted = $tokens->delete("foo");

        $this->assertTrue($deleted);
        $this->assertSame(["Foo", "FOO", "Bar"], $tokens->getArray());
    }

    public function testDeleteRemovesAllCaseInsensitiveMatchesWhenChangeCaseTrue(): void
    {
        $tokens = new DsvTokens(changeCase: true, uniqueTokensOnly: false);
        $tokens->add("Foo", "foo", "FOO", "Bar");

        $deleted = $tokens->delete("foo");

        $this->assertTrue($deleted);
        $this->assertSame(["bar"], $tokens->getArray());
    }

    public function testFluentChainingWorksAndIgnoresEmpties(): void
    {
        $tokens = new DsvTokens(uniqueTokensOnly: false);

        $tokens
            ->add("a")
            ->add("b", "")
            ->add("  ", "B")  // lowercased due to default changeCase=true
            ->delete("c");     // not present; should be no-op returning false internally

        $this->assertSame(["a", "b", "b"], $tokens->getArray());
    }

    public function testAddWithNoTokensReturnsSelfAndNoChange(): void
    {
        $tokens = new DsvTokens();
        $before = $tokens->getArray();

        $returned = $tokens->add();

        $this->assertSame($tokens, $returned);
        $this->assertSame($before, $tokens->getArray());
    }

    // ... existing code ...
    public function testAddSkipsWhitespaceOnlyAndControlBytesThatTrimToEmpty(): void
    {
        $tokens = new DsvTokens();
        // "\0" and "\r\n" are trimmed to empty; " a " becomes "a"
        $tokens->add("\0", "\r\n", " \t ", " a ");

        $this->assertSame(["a"], $tokens->getArray());
    }

    // ... existing code ...
    public function testAddBinaryBytesAreKeptAndAsciiIsLowercasedByDefault(): void
    {
        $tokens = new DsvTokens(); // changeCase=true
        // "\x80" is non-ASCII and unaffected by strtolower; "A" becomes "a"
        $tokens->add("A\x80", "B");

        $this->assertSame(["a\x80", "b"], $tokens->getArray());
    }

    // ... existing code ...
    public function testAddKeepsOriginalCaseWhenChangeCaseFalse(): void
    {
        $tokens = new DsvTokens(changeCase: false);
        $tokens->add("A", "a", "B");

        // With uniqueTokensOnly=true (default), first occurrences are kept
        $this->assertSame(["A", "B"], $tokens->getArray());
    }

    // ... existing code ...
    public function testAddAllowsDuplicatesWhenUniqueTokensOnlyFalse(): void
    {
        $tokens = new DsvTokens(changeCase: true, uniqueTokensOnly: false);
        $tokens->add("X", "x", "x");

        $this->assertSame(["x", "x", "x"], $tokens->getArray());
    }

    // ... existing code ...
    public function testDeleteTrimsInputAndRemovesMatches(): void
    {
        $tokens = new DsvTokens(uniqueTokensOnly: false);
        $tokens->add("foo", "bar", "baz");

        $deleted = $tokens->delete("  foo\t");

        $this->assertTrue($deleted);
        $this->assertSame(["bar", "baz"], $tokens->getArray());
    }

    // ... existing code ...
    public function testDeleteReturnsFalseForEmptyOrWhitespace(): void
    {
        $tokens = new DsvTokens();
        $tokens->add("a");

        $this->assertFalse($tokens->delete(""));
        $this->assertFalse($tokens->delete("   "));
        $this->assertFalse($tokens->delete("\0"));
        $this->assertSame(["a"], $tokens->getArray());
    }

    // ... existing code ...
    public function testDeleteRemovesOnlyExactMatchesWhenChangeCaseFalse(): void
    {
        $tokens = new DsvTokens(changeCase: false, uniqueTokensOnly: false);
        $tokens->add("Foo", "foo", "FOO", "Bar");

        $deleted = $tokens->delete("foo");

        $this->assertTrue($deleted);
        $this->assertSame(["Foo", "FOO", "Bar"], $tokens->getArray());
    }

    // ... existing code ...
    public function testOrderStabilityAfterMultipleDeletes(): void
    {
        $tokens = new DsvTokens(uniqueTokensOnly: false);
        $tokens->add("a", "b", "c", "b", "d");

        $tokens->delete("b");

        // With changeCase=true, both "b" entries are removed and order of remaining bytes is preserved
        $this->assertSame(["a", "c", "d"], $tokens->getArray());
    }

    // ... existing code ...
    public function testFluentChainingWithBinaryAndWhitespaceInputs(): void
    {
        $tokens = new DsvTokens(uniqueTokensOnly: false);

        $tokens
            ->add(" a ", "\t", "\0", "b")
            ->add("A", "B")
            ->delete("A");   // normalized to "a" when changeCase=true

        $tokens->add("c");

        // "\t" trims to empty, "\0" trims to empty; "A" deleted along with existing "a"
        $this->assertSame(["b", "b", "c"], $tokens->getArray());
    }

    // ... existing code ...
    public function testJoinAcceptsSingleByteGlueIncludingControlAndNul(): void
    {
        $tokens = new DsvTokens(changeCase: false, uniqueTokensOnly: false);
        $tokens->add("A", "b", "C");

        $this->assertSame("A,b,C", $tokens->join(","));
        $this->assertSame("A\tb\tC", $tokens->join("\t"));
        $this->assertSame("A\0b\0C", $tokens->join("\0"));
    }
}