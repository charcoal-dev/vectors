<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Tests\Fixtures;

enum TestEnumBacked: string
{
    case One = 'one';
    case Two = 'two';
    case Three = 'three';
}