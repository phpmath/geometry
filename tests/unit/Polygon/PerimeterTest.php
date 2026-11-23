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

use function sqrt;

#[CoversClass(Polygon::class)]
#[CoversMethod(Polygon::class, 'perimeter')]
final class PerimeterTest extends TestCase
{
    public function testReturnsSumOfEdgeLengths(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $perimeter = $polygon->perimeter();

        // Assert
        self::assertEqualsWithDelta(16.0, $perimeter, 1e-9);
    }

    public function testIncludesClosingEdge(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(6, 0), new Point(0, 3)]);

        // Act
        $perimeter = $polygon->perimeter();

        // Assert
        self::assertEqualsWithDelta(9 + sqrt(45), $perimeter, 1e-9);
    }
}
