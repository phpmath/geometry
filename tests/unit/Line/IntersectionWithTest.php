<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Line;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const M_PI;
use const NAN;

#[CoversClass(Line::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Line::class, 'intersectionWith')]
final class IntersectionWithTest extends TestCase
{
    public function testReturnsCrossingPoint(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(2, 2));
        $b = Line::through(new Point(0, 4), new Point(4, 0));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNotNull($point);
        self::assertEqualsWithDelta(2.0, $point->x, 1e-9);
        self::assertEqualsWithDelta(2.0, $point->y, 1e-9);
    }

    public function testFindsCrossingBeyondDefiningPoints(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(1, 0));
        $b = Line::through(new Point(5, 1), new Point(5, 2));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNotNull($point);
        self::assertEqualsWithDelta(5.0, $point->x, 1e-9);
        self::assertEqualsWithDelta(0.0, $point->y, 1e-9);
    }

    public function testReturnsNullForParallelLines(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(1, 0));
        $b = Line::through(new Point(0, 1), new Point(1, 1));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    public function testReturnsNullForIdenticalLines(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(1, 1));
        $b = Line::through(new Point(2, 2), new Point(3, 3));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNull($point);
    }

    public function testReturnsExactOriginForAxes(): void
    {
        // Arrange
        $xAxis = new Line(new Point(0, 0), 0);
        $yAxis = new Line(new Point(0, 0), M_PI / 2);

        // Act
        $point = $xAxis->intersectionWith($yAxis);

        // Assert
        self::assertNotNull($point);
        self::assertSame(0.0, $point->x);
        self::assertSame(0.0, $point->y);
    }

    public function testReturnsExactOriginForDiagonalAndXAxis(): void
    {
        // Arrange
        $xAxis = new Line(new Point(0, 0), 0);
        $diagonal = Line::through(new Point(0, 0), new Point(1, 1));

        // Act
        $point = $diagonal->intersectionWith($xAxis);

        // Assert
        self::assertNotNull($point);
        self::assertSame(0.0, $point->x);
        self::assertSame(0.0, $point->y);
    }

    public function testReturnsExactPointForOffsetAxes(): void
    {
        // Arrange
        $horizontal = new Line(new Point(0, 2), 0);
        $vertical = new Line(new Point(5, 0), M_PI / 2);

        // Act
        $point = $horizontal->intersectionWith($vertical);

        // Assert
        self::assertNotNull($point);
        self::assertSame(5.0, $point->x);
        self::assertSame(2.0, $point->y);
    }

    public function testReturnsExactPointForDiagonalAndHorizontalLine(): void
    {
        // Arrange
        $diagonal = Line::through(new Point(0, 0), new Point(1, 1));
        $horizontal = new Line(new Point(0, -3), 0);

        // Act
        $point = $diagonal->intersectionWith($horizontal);

        // Assert
        self::assertNotNull($point);
        self::assertSame(-3.0, $point->x);
        self::assertSame(-3.0, $point->y);
    }

    public function testReturnsExactPointForTwoDiagonals(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(2, 2));
        $b = Line::through(new Point(0, 4), new Point(4, 0));

        // Act
        $point = $a->intersectionWith($b);

        // Assert
        self::assertNotNull($point);
        self::assertSame(2.0, $point->x);
        self::assertSame(2.0, $point->y);
    }

    public function testReturnsExactPointForThreeQuarterTurn(): void
    {
        // Arrange
        $down = new Line(new Point(1, 5), 3 * M_PI / 2);
        $horizontal = new Line(new Point(0, 2), 0);

        // Act
        $point = $down->intersectionWith($horizontal);

        // Assert
        self::assertNotNull($point);
        self::assertSame(1.0, $point->x);
        self::assertSame(2.0, $point->y);
    }

    public function testReturnsExactPointForNegativeQuarterTurn(): void
    {
        // Arrange
        $down = new Line(new Point(1, 5), -M_PI / 2);
        $horizontal = new Line(new Point(0, 2), 0);

        // Act
        $point = $down->intersectionWith($horizontal);

        // Assert
        self::assertNotNull($point);
        self::assertSame(1.0, $point->x);
        self::assertSame(2.0, $point->y);
    }

    public function testReturnsNullWithinCustomTolerance(): void
    {
        // Arrange
        $a = new Line(new Point(0, 0), 0);
        $b = new Line(new Point(0, 1), 0.001);

        // Act
        $point = $a->intersectionWith($b, 0.01);

        // Assert
        self::assertNull($point);
    }

    public function testFindsCrossingOfNearlyParallelLinesWithZeroTolerance(): void
    {
        // Arrange
        $a = new Line(new Point(0, 0), 0);
        $b = new Line(new Point(0, 1), 1e-10);

        // Act
        $point = $a->intersectionWith($b, 0.0);

        // Assert
        self::assertNotNull($point);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $xAxis = new Line(new Point(0, 0), 0);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $xAxis->intersectionWith($xAxis, $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
