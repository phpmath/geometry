<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Line;

use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Line::class)]
#[CoversMethod(Line::class, 'distanceTo')]
final class DistanceToTest extends TestCase
{
    public function testReturnsPerpendicularDistance(): void
    {
        // Arrange
        $line = Line::through(new Point(0, 0), new Point(1, 0));

        // Act
        $distance = $line->distanceTo(new Point(7, 3));

        // Assert
        self::assertEqualsWithDelta(3.0, $distance, 1e-9);
    }

    public function testReturnsZeroForPointOnLine(): void
    {
        // Arrange
        $line = Line::through(new Point(0, 0), new Point(1, 1));

        // Act
        $distance = $line->distanceTo(new Point(-5, -5));

        // Assert
        self::assertEqualsWithDelta(0.0, $distance, 1e-9);
    }

    public function testReturnsPositiveDistanceOnEitherSide(): void
    {
        // Arrange
        $line = Line::through(new Point(0, 0), new Point(1, 0));

        // Act
        $distance = $line->distanceTo(new Point(7, -3));

        // Assert
        self::assertEqualsWithDelta(3.0, $distance, 1e-9);
    }

    public function testReturnsExactDistanceForDiagonalLine(): void
    {
        // Arrange
        $line = Line::through(new Point(0, 0), new Point(1, 1));

        // Act
        $distance = $line->distanceTo(new Point(1, 1));

        // Assert
        self::assertSame(0.0, $distance);
    }
}
