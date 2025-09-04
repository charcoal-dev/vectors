<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Tests;

use Charcoal\Vectors\Support\ExceptionBag;

/**
 * Unit test class for the ExceptionBag class.
 */
final class ExceptionBagTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorStoresExceptionsAndPreservesOrder(): void
    {
        $e1 = new \Exception("e1");
        $e2 = new \RuntimeException("e2");
        $bag = new ExceptionBag($e1, $e2);

        $this->assertSame(2, $bag->count());
        $this->assertSame([$e1, $e2], $bag->getArray());
    }

    public function testAppendAddsExceptionAndChains(): void
    {
        $bag = new ExceptionBag();
        $e = new \Exception("boom");

        $ret = $bag->append($e);

        $this->assertSame($bag, $ret);
        $this->assertSame(1, $bag->count());
        $this->assertSame([$e], $bag->getArray());
    }

    public function testIteratorYieldsAllInInsertionOrder(): void
    {
        $e1 = new \Exception("a");
        $e2 = new \RuntimeException("b");
        $e3 = new \LogicException("c");
        $bag = new ExceptionBag($e1, $e2, $e3);

        $seen = [];
        foreach ($bag as $ex) {
            $seen[] = $ex;
        }

        $this->assertSame([$e1, $e2, $e3], $seen);
    }

    public function testGetArrayIsADetachedCopyNotByReference(): void
    {
        $e1 = new \Exception("x");
        $bag = new ExceptionBag($e1);

        $arr = $bag->getArray();
        $arr[] = new \Exception("y");

        $this->assertSame(1, $bag->count());
        $this->assertSame([$e1], $bag->getArray());
    }

    public function testAllowsDuplicateReferencesAndDifferentTypes(): void
    {
        $e1 = new \Exception("dup");
        $e2 = new \RuntimeException("rt");
        $bag = new ExceptionBag($e1);

        $bag->append($e1)->append($e2)->append(new \Exception("new"));

        $this->assertSame(4, $bag->count());
        $this->assertSame([$e1, $e1, $e2, $bag->getArray()[3]], $bag->getArray());
        $this->assertInstanceOf(\Exception::class, $bag->getArray()[0]);
        $this->assertInstanceOf(\RuntimeException::class, $bag->getArray()[2]);
    }

    public function testCountReflectsGrowthAccuratelyUnderStress(): void
    {
        $bag = new ExceptionBag();
        $n = 1000;
        for ($i = 0; $i < $n; $i++) {
            $bag->append(new \Exception("e" . $i));
        }
        $this->assertSame($n, $bag->count());
    }

    public function testAppendNullMessageOrPreviousExceptionStillStoresObject(): void
    {
        $prev = new \Exception("prev");
        $e = new \Exception("", 0, $prev);
        $bag = new ExceptionBag();
        $bag->append($e);

        $this->assertSame(1, $bag->count());
        $this->assertSame($e, $bag->getArray()[0]);
        $this->assertSame($prev, $bag->getArray()[0]->getPrevious());
    }

    public function testEmptyBagIsIterableAndCountZero(): void
    {
        $bag = new ExceptionBag();

        $this->assertSame(0, $bag->count());

        $collected = [];
        foreach ($bag as $ex) {
            $collected[] = $ex;
        }
        $this->assertSame([], $collected);
    }
}