<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Point;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Point::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Point::class, 'equals')]
final class EqualsTest extends TestCase
{
    public function testReturnsTrueForIdenticalPoints(): void
    {
        // Arrange
        $a = new Point(1, 2);
        $b = new Point(1, 2);

        // Act
        $result = $a->equals($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueWithinDefaultTolerance(): void
    {
        // Arrange
        $a = new Point(1, 2);
        $b = new Point(1.0 + 1e-12, 2);

        // Act
        $result = $a->equals($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseOutsideDefaultTolerance(): void
    {
        // Arrange
        $a = new Point(1, 2);
        $b = new Point(1.001, 2);

        // Act
        $result = $a->equals($b);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueWithinCustomTolerance(): void
    {
        // Arrange
        $a = new Point(1, 2);
        $b = new Point(1.001, 2.001);

        // Act
        $result = $a->equals($b, 0.01);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseWhenOnlyYDiffers(): void
    {
        // Arrange
        $a = new Point(1, 2);
        $b = new Point(1, 3);

        // Act
        $result = $a->equals($b);

        // Assert
        self::assertFalse($result);
    }

    public function testComparesDistanceInsteadOfCoordinates(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(0.08, 0.08);

        // Act
        $result = $a->equals($b, 0.1);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueWhenDistanceEqualsTolerance(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(3, 4);

        // Act
        $result = $a->equals($b, 5.0);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForZeroToleranceOnIdenticalPoints(): void
    {
        // Arrange
        $a = new Point(1, 2);
        $b = new Point(1, 2);

        // Act
        $result = $a->equals($b, 0.0);

        // Assert
        self::assertTrue($result);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $a = new Point(1, 2);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $a->equals(new Point(1, 2), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
