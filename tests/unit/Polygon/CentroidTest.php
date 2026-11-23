<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Polygon;

use PHPMath\Geometry\Exception\DegeneratePolygonException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Polygon::class)]
#[CoversClass(DegeneratePolygonException::class)]
#[CoversMethod(Polygon::class, 'centroid')]
final class CentroidTest extends TestCase
{
    public function testReturnsCenterOfSquare(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $centroid = $polygon->centroid();

        // Assert
        self::assertEqualsWithDelta(2.0, $centroid->x, 1e-9);
        self::assertEqualsWithDelta(2.0, $centroid->y, 1e-9);
    }

    public function testReturnsCentroidOfTriangle(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(6, 0), new Point(0, 3)]);

        // Act
        $centroid = $polygon->centroid();

        // Assert
        self::assertEqualsWithDelta(2.0, $centroid->x, 1e-9);
        self::assertEqualsWithDelta(1.0, $centroid->y, 1e-9);
    }

    public function testReturnsSameCentroidForClockwisePolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 4), new Point(4, 4), new Point(4, 0), new Point(0, 0)]);

        // Act
        $centroid = $polygon->centroid();

        // Assert
        self::assertEqualsWithDelta(2.0, $centroid->x, 1e-9);
        self::assertEqualsWithDelta(2.0, $centroid->y, 1e-9);
    }

    public function testThrowsForZeroAreaPolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);
        $this->expectException(DegeneratePolygonException::class);

        // Act
        $polygon->centroid();
    }

    public function testReturnsCentroidOfTinyPolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(3e-6, 0), new Point(0, 3e-6)]);

        // Act
        $centroid = $polygon->centroid();

        // Assert
        self::assertEqualsWithDelta(1e-6, $centroid->x, 1e-15);
        self::assertEqualsWithDelta(1e-6, $centroid->y, 1e-15);
    }

    public function testReturnsExactCentroidFarFromOrigin(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(1e8, 1e8), new Point(1e8 + 6, 1e8), new Point(1e8, 1e8 + 3)]);

        // Act
        $centroid = $polygon->centroid();

        // Assert
        self::assertSame(1e8 + 2, $centroid->x);
        self::assertSame(1e8 + 1, $centroid->y);
    }
}
