<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Polygon;

use PHPMath\Geometry\Orientation;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Polygon::class)]
#[CoversMethod(Polygon::class, 'orientation')]
final class OrientationTest extends TestCase
{
    public function testReturnsCounterClockwise(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $orientation = $polygon->orientation();

        // Assert
        self::assertSame(Orientation::CounterClockwise, $orientation);
    }

    public function testReturnsClockwise(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 4), new Point(4, 4), new Point(4, 0), new Point(0, 0)]);

        // Act
        $orientation = $polygon->orientation();

        // Assert
        self::assertSame(Orientation::Clockwise, $orientation);
    }

    public function testReturnsDegenerateForCollinearPoints(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

        // Act
        $orientation = $polygon->orientation();

        // Assert
        self::assertSame(Orientation::Degenerate, $orientation);
    }

    public function testReturnsOrientationOfTinyPolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(1e-9, 0), new Point(0, 1e-9)]);

        // Act
        $orientation = $polygon->orientation();

        // Assert
        self::assertSame(Orientation::CounterClockwise, $orientation);
    }
}
