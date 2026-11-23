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
#[CoversMethod(Polygon::class, 'contains')]
final class ContainsTest extends TestCase
{
    public function testReturnsTrueForInteriorPoint(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(2, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForExteriorPoint(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(5, 2));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueForPointOnEdge(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(4, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForVertex(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(0, 0));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForPointInConcavity(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(2, 1), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(2, 3));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueForPointInConcavePolygonArm(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(2, 1), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(0.5, 3));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForPointAlignedWithHorizontalEdge(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(-1, 4));

        // Assert
        self::assertFalse($result);
    }

    public function testHandlesDuplicateConsecutivePoints(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(2, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForDuplicatePointItself(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(0, 0));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueOutsideEdgeWithinCustomTolerance(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(4.05, 2), 0.1);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseOutsideEdgeWithZeroTolerance(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(4.05, 2), 0.0);

        // Assert
        self::assertFalse($result);
    }

    public function testUsesToleranceForDuplicatePoint(): void
    {
        // Arrange
        $polygon = new Polygon([new Point(0, 0), new Point(0, 0), new Point(4, 0), new Point(0, 4)]);

        // Act
        $result = $polygon->contains(new Point(-0.05, -0.05), 0.1);

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
        $polygon->contains(new Point(1, 1), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
