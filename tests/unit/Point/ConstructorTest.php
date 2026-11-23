<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Point;

use PHPMath\Geometry\Exception\NonFiniteValueException;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Point::class)]
#[CoversClass(NonFiniteValueException::class)]
#[CoversMethod(Point::class, '__construct')]
final class ConstructorTest extends TestCase
{
    public function testStoresCoordinates(): void
    {
        // Arrange

        // Act
        $point = new Point(1.5, -2.5);

        // Assert
        self::assertSame(1.5, $point->x);
        self::assertSame(-2.5, $point->y);
    }

    public function testThrowsOnNonFiniteX(): void
    {
        // Arrange
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Point(NAN, 0.0);
    }

    public function testThrowsOnNonFiniteY(): void
    {
        // Arrange
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Point(0.0, INF);
    }
}
