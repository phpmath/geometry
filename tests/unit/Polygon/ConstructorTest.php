<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Polygon;

use PHPMath\Geometry\Exception\InvalidPointException;
use PHPMath\Geometry\Exception\TooFewPointsException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Polygon::class)]
#[CoversClass(TooFewPointsException::class)]
#[CoversClass(InvalidPointException::class)]
#[CoversMethod(Polygon::class, '__construct')]
final class ConstructorTest extends TestCase
{
    public function testStoresPoints(): void
    {
        // Arrange
        $points = [new Point(0, 0), new Point(1, 0), new Point(0, 1)];

        // Act
        $polygon = new Polygon($points);

        // Assert
        self::assertSame($points, $polygon->points);
    }

    public function testThrowsOnTooFewPoints(): void
    {
        // Arrange
        $this->expectException(TooFewPointsException::class);

        // Act
        new Polygon([new Point(0, 0), new Point(1, 1)]);
    }

    public function testReindexesPointsWithOtherKeys(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(1, 0);
        $c = new Point(0, 1);

        // Act
        $polygon = new Polygon([5 => $a, 'x' => $b, 2 => $c]);

        // Assert
        self::assertSame([$a, $b, $c], $polygon->points);
    }

    public function testThrowsOnValueThatIsNotAPoint(): void
    {
        // Arrange
        $this->expectException(InvalidPointException::class);
        $this->expectExceptionMessage(
            'The polygon point at position 1 must be an instance of PHPMath\\Geometry\\Point, "string" given.',
        );

        // Act
        new Polygon([new Point(0, 0), '1, 1', new Point(1, 0)]);
    }

    public function testThrowsOnInvalidPointBeforeCountingPoints(): void
    {
        // Arrange
        $this->expectException(InvalidPointException::class);

        // Act
        new Polygon([new Point(0, 0), null]);
    }
}
