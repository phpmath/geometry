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
use const NAN;

#[CoversClass(Line::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Line::class, 'isParallelTo')]
final class IsParallelToTest extends TestCase
{
    public function testReturnsTrueForParallelLines(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(1, 1));
        $b = Line::through(new Point(0, 5), new Point(3, 8));

        // Act
        $result = $a->isParallelTo($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForOppositeDirections(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(1, 0));
        $b = Line::through(new Point(0, 1), new Point(-1, 1));

        // Act
        $result = $a->isParallelTo($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForCrossingLines(): void
    {
        // Arrange
        $a = Line::through(new Point(0, 0), new Point(1, 0));
        $b = Line::through(new Point(0, 0), new Point(0, 1));

        // Act
        $result = $a->isParallelTo($b);

        // Assert
        self::assertFalse($result);
    }

    public function testUsesCustomTolerance(): void
    {
        // Arrange
        $a = new Line(new Point(0, 0), 0);
        $b = new Line(new Point(0, 0), 0.001);

        // Act
        $result = $a->isParallelTo($b, 0.01);

        // Assert
        self::assertTrue($result);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $a = new Line(new Point(0, 0), 0);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $a->isParallelTo($a, $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
