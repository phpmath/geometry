<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Polygon;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Polygon::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Polygon::class, 'isConvex')]
final class IsConvexTest extends TestCase
{
    public function testReturnsTrueForSquare(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->isConvex();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForClockwiseSquare(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 4), new Point(4, 4), new Point(4, 0), new Point(0, 0)]);

        // Act
        $result = $polygon->isConvex();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForConcavePolygon(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(2, 1), new Point(0, 4)]);

        // Act
        $result = $polygon->isConvex();

        // Assert
        self::assertFalse($result);
    }

    public function testIgnoresCollinearVertices(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(2, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->isConvex();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForPolygonWithoutArea(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

        // Act
        $result = $polygon->isConvex();

        // Assert
        self::assertFalse($result);
    }

    public function testTreatsSmallDentAsCollinearWithCustomTolerance(): void
    {
        // Arrange
        $polygon = new Polygon([
            new Point(0, 0),
            new Point(2, 0.05),
            new Point(4, 0),
            new Point(4, 4),
            new Point(0, 4),
        ]);

        // Act
        $result = $polygon->isConvex(0.2);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForSmallDentWithDefaultTolerance(): void
    {
        // Arrange
        $polygon = new Polygon([
            new Point(0, 0),
            new Point(2, 0.05),
            new Point(4, 0),
            new Point(4, 4),
            new Point(0, 4),
        ]);

        // Act
        $result = $polygon->isConvex();

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueForTinyPolygonWithZeroTolerance(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(1e-12, 0), new Point(0, 1e-12)]);

        // Act
        $result = $polygon->isConvex(0.0);

        // Assert
        self::assertTrue($result);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $polygon->isConvex($tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
