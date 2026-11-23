<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Segment;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Segment::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Segment::class, 'intersectionWith')]
final class IntersectionWithTest extends TestCase
{
    public function testReturnsCrossingPoint(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(2, 2));
        $b = new Segment(new Point(0, 2), new Point(2, 0));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNotNull($point);
        self::assertEqualsWithDelta(1.0, $point->x, 1e-9);
        self::assertEqualsWithDelta(1.0, $point->y, 1e-9);
    }

    public function testReturnsEndpointWhenSegmentsTouch(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(2, 0));
        $b = new Segment(new Point(2, 0), new Point(2, 2));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNotNull($point);
        self::assertEqualsWithDelta(2.0, $point->x, 1e-9);
        self::assertEqualsWithDelta(0.0, $point->y, 1e-9);
    }

    public function testReturnsNullWhenLinesCrossOutsideSegments(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(1, 1));
        $b = new Segment(new Point(0, 4), new Point(4, 0));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    public function testReturnsNullForParallelSegments(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(2, 0));
        $b = new Segment(new Point(0, 1), new Point(2, 1));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    public function testReturnsNullForCollinearOverlappingSegments(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(2, 0));
        $b = new Segment(new Point(1, 0), new Point(3, 0));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    public function testReturnsPointBeyondEndWithinCustomTolerance(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(4, 0));
        $b = new Segment(new Point(4.05, -1), new Point(4.05, 1));

        // Act
        $point = $a->intersectionWith($b, 0.1);

        // Assert
        self::assertNotNull($point);
        self::assertEqualsWithDelta(4.05, $point->x, 1e-9);
        self::assertEqualsWithDelta(0.0, $point->y, 1e-9);
    }

    public function testReturnsNullBeyondEndWithDefaultTolerance(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(4, 0));
        $b = new Segment(new Point(4.05, -1), new Point(4.05, 1));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    public function testDetectsCrossingOfSmallSegments(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(1e-6, 1e-6));
        $b = new Segment(new Point(0, 1e-6), new Point(1e-6, 0));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNotNull($point);
        self::assertEqualsWithDelta(5e-7, $point->x, 1e-15);
    }

    public function testReturnsNullForNearlyParallelLongSegments(): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(1e6, 0));
        $b = new Segment(new Point(0, 1), new Point(1e6, 1 + 1e-9));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $a = new Segment(new Point(0, 0), new Point(4, 4));
        $this->expectException(InvalidToleranceException::class);

        // Act
        $a->intersectionWith(new Segment(new Point(0, 4), new Point(4, 0)), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
