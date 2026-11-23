<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Rectangle;

use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Rectangle::class)]
#[CoversMethod(Rectangle::class, 'fromCorners')]
final class FromCornersTest extends TestCase
{
    public function testCreatesRectangleFromOppositeCornersInAnyOrder(): void
    {
        // Arrange
        $a = new Point(4, 5);
        $b = new Point(1, 1);

        // Act
        $rectangle = Rectangle::fromCorners($a, $b);

        // Assert
        self::assertSame(1.0, $rectangle->origin->x);
        self::assertSame(1.0, $rectangle->origin->y);
        self::assertSame(3.0, $rectangle->width);
        self::assertSame(4.0, $rectangle->height);
    }
}
