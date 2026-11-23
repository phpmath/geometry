<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Polygon;

use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Polygon::class)]
#[CoversMethod(Polygon::class, 'area')]
final class AreaTest extends TestCase
{
    public function testReturnsAreaOfSquare(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $area = $polygon->area();

        // Assert
        self::assertEqualsWithDelta(16.0, $area, 1e-9);
    }

    public function testReturnsAreaOfTriangle(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(6, 0), new Point(0, 3)]);

        // Act
        $area = $polygon->area();

        // Assert
        self::assertEqualsWithDelta(9.0, $area, 1e-9);
    }

    public function testReturnsPositiveAreaForClockwisePolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 4), new Point(4, 4), new Point(4, 0), new Point(0, 0)]);

        // Act
        $area = $polygon->area();

        // Assert
        self::assertEqualsWithDelta(16.0, $area, 1e-9);
    }

    public function testReturnsAreaOfConcavePolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(2, 1), new Point(0, 4)]);

        // Act
        $area = $polygon->area();

        // Assert
        self::assertEqualsWithDelta(10.0, $area, 1e-9);
    }

    public function testWorksWithPointsGivenWithOtherKeys(): void
    {
        // Arrange
        $polygon = new Polygon([
            7 => new Point(0, 0),
            'a' => new Point(4, 0),
            2 => new Point(4, 4),
            0 => new Point(0, 4),
        ]);

        // Act
        $area = $polygon->area();

        // Assert
        self::assertSame(16.0, $area);
    }
}
