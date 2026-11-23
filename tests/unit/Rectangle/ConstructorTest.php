<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Rectangle;

use PHPMath\Geometry\Exception\NegativeSizeException;
use PHPMath\Geometry\Exception\NonFiniteValueException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Rectangle::class)]
#[CoversClass(NonFiniteValueException::class)]
#[CoversClass(NegativeSizeException::class)]
#[CoversMethod(Rectangle::class, '__construct')]
final class ConstructorTest extends TestCase
{
    public function testStoresOriginAndSize(): void
    {
        // Arrange
        $origin = new Point(1, 2);

        // Act
        $rectangle = new Rectangle($origin, 3.0, 4.0);

        // Assert
        self::assertSame($origin, $rectangle->origin);
        self::assertSame(3.0, $rectangle->width);
        self::assertSame(4.0, $rectangle->height);
    }

    public function testThrowsOnNegativeWidth(): void
    {
        // Arrange
        $origin = new Point(0, 0);
        $this->expectException(NegativeSizeException::class);

        // Act
        new Rectangle($origin, -1.0, 1.0);
    }

    public function testThrowsOnNegativeHeight(): void
    {
        // Arrange
        $origin = new Point(0, 0);
        $this->expectException(NegativeSizeException::class);

        // Act
        new Rectangle($origin, 1.0, -1.0);
    }

    public function testThrowsOnNonFiniteWidth(): void
    {
        // Arrange
        $origin = new Point(0, 0);
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Rectangle($origin, NAN, 1.0);
    }

    public function testThrowsOnNonFiniteHeight(): void
    {
        // Arrange
        $origin = new Point(0, 0);
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Rectangle($origin, 1.0, INF);
    }
}
