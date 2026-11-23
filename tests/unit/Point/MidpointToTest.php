<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Point;

use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Point::class)]
#[CoversMethod(Point::class, 'midpointTo')]
final class MidpointToTest extends TestCase
{
    public function testReturnsPointHalfway(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(4, -2);

        // Act
        $midpoint = $a->midpointTo($b);

        // Assert
        self::assertSame(2.0, $midpoint->x);
        self::assertSame(-1.0, $midpoint->y);
    }
}
