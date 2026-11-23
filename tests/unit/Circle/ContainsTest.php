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
#[CoversMethod(Circle::class, 'contains')]
final class ContainsTest extends TestCase
{
    public function testReturnsTrueForInteriorPoint(): void
    {
        // Arrange
        $circle = new Circle(new Point(1, 1), 2);

        // Act
        $result = $circle->contains(new Point(2, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForPointOnBoundary(): void
    {
        // Arrange
        $circle = new Circle(new Point(0, 0), 5);

        // Act
        $result = $circle->contains(new Point(3, 4));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForExteriorPoint(): void
    {
        // Arrange
        $circle = new Circle(new Point(0, 0), 5);

        // Act
        $result = $circle->contains(new Point(4, 4));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueOnBoundaryWithZeroTolerance(): void
    {
        // Arrange
        $circle = new Circle(new Point(0, 0), 5);

        // Act
        $result = $circle->contains(new Point(3, 4), 0.0);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseOutsideWithZeroTolerance(): void
    {
        // Arrange
        $circle = new Circle(new Point(0, 0), 5);

        // Act
        $result = $circle->contains(new Point(0, 5.001), 0.0);

        // Assert
        self::assertFalse($result);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $circle = new Circle(new Point(0, 0), 5);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $circle->contains(new Point(1, 1), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
