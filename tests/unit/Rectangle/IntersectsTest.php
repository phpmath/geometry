<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Rectangle;

use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Rectangle::class)]
#[CoversMethod(Rectangle::class, 'intersects')]
final class IntersectsTest extends TestCase
{
    public function testReturnsTrueForOverlappingRectangles(): void
    {
        // Arrange
        $a = new Rectangle(new Point(0, 0), 4, 4);
        $b = new Rectangle(new Point(2, 2), 4, 4);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueWhenOneContainsTheOther(): void
    {
        // Arrange
        $a = new Rectangle(new Point(0, 0), 10, 10);
        $b = new Rectangle(new Point(2, 2), 1, 1);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForTouchingEdges(): void
    {
        // Arrange
        $a = new Rectangle(new Point(0, 0), 4, 4);
        $b = new Rectangle(new Point(4, 0), 4, 4);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForSeparateRectanglesOnXAxis(): void
    {
        // Arrange
        $a = new Rectangle(new Point(0, 0), 1, 1);
        $b = new Rectangle(new Point(5, 0), 1, 1);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForSeparateRectanglesOnYAxis(): void
    {
        // Arrange
        $a = new Rectangle(new Point(0, 0), 1, 1);
        $b = new Rectangle(new Point(0, 5), 1, 1);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseWhenOtherIsLeftOfRectangle(): void
    {
        // Arrange
        $a = new Rectangle(new Point(5, 0), 1, 1);
        $b = new Rectangle(new Point(0, 0), 1, 1);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseWhenOtherIsBelowRectangle(): void
    {
        // Arrange
        $a = new Rectangle(new Point(0, 5), 1, 1);
        $b = new Rectangle(new Point(0, 0), 1, 1);

        // Act
        $result = $a->intersects($b);

        // Assert
        self::assertFalse($result);
    }
}
