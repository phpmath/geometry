<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Circle;

use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Circle::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Circle::class, 'intersectionsWith')]
final class IntersectionsWithTest extends TestCase
{
    public function testReturnsTwoPointsForOverlappingCircles(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $b = new Circle(new Point(6, 0), 5);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertCount(2, $points);
        self::assertEqualsWithDelta(3.0, $points[0]->x, 1e-9);
        self::assertEqualsWithDelta(4.0, $points[0]->y, 1e-9);
        self::assertEqualsWithDelta(3.0, $points[1]->x, 1e-9);
        self::assertEqualsWithDelta(-4.0, $points[1]->y, 1e-9);
    }

    public function testReturnsOnePointForExternallyTangentCircles(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 1);
        $b = new Circle(new Point(3, 0), 2);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertCount(1, $points);
        self::assertEqualsWithDelta(1.0, $points[0]->x, 1e-9);
        self::assertEqualsWithDelta(0.0, $points[0]->y, 1e-9);
    }

    public function testReturnsOnePointForInternallyTangentCircles(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 3);
        $b = new Circle(new Point(1, 0), 2);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertCount(1, $points);
        self::assertEqualsWithDelta(3.0, $points[0]->x, 1e-9);
        self::assertEqualsWithDelta(0.0, $points[0]->y, 1e-9);
    }

    public function testReturnsNoPointsForSeparateCircles(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 1);
        $b = new Circle(new Point(5, 0), 1);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertSame([], $points);
    }

    public function testReturnsNoPointsWhenOneCircleContainsTheOther(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $b = new Circle(new Point(1, 0), 1);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertSame([], $points);
    }

    public function testReturnsNoPointsForConcentricCircles(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $b = new Circle(new Point(0, 0), 5);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertSame([], $points);
    }

    public function testTreatsNearMissAsTangentWithCustomTolerance(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $b = new Circle(new Point(10.05, 0), 5);

        // Act
        $points = $a->intersectionsWith($b, 0.1);

        // Assert
        self::assertCount(1, $points);
    }

    public function testReturnsNoPointsForNearMissWithDefaultTolerance(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $b = new Circle(new Point(10.05, 0), 5);

        // Act
        $points = $a->intersectionsWith($b);

        // Assert
        self::assertSame([], $points);
    }

    public function testTreatsCloseCentersAsConcentricWithCustomTolerance(): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $b = new Circle(new Point(0.05, 0), 5);

        // Act
        $points = $a->intersectionsWith($b, 0.1);

        // Assert
        self::assertSame([], $points);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $a = new Circle(new Point(0, 0), 5);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $a->intersectionsWith(new Circle(new Point(6, 0), 5), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
