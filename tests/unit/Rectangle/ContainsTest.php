<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Rectangle;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Rectangle::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Rectangle::class, 'contains')]
final class ContainsTest extends TestCase
{
    public function testReturnsTrueForInteriorPoint(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(2, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForPointOnEdge(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(4, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForPointRightOfRectangle(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(5, 2));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForPointAboveRectangle(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(2, 5));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForPointLeftOfRectangle(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(-1, 2));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForPointBelowRectangle(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(2, -1));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueWithinDefaultToleranceOutsideEdge(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(4 + 1e-10, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseOutsideDefaultTolerance(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(4.001, 2));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueWithinCustomToleranceOutsideEdge(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(4.05, 2), 0.1);

        // Assert
        self::assertTrue($result);
    }

    public function testMeasuresToleranceDiagonallyBeyondCorner(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(4.08, 4.08), 0.1);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseOutsideWithZeroTolerance(): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);

        // Act
        $result = $r->contains(new Point(-1e-12, 2), 0.0);

        // Assert
        self::assertFalse($result);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $r = new Rectangle(new Point(0, 0), 4, 4);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $r->contains(new Point(1, 1), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
