<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Circle;

use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Exception\NegativeRadiusException;
use PHPMath\Geometry\Exception\NonFiniteValueException;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;

#[CoversClass(Circle::class)]
#[CoversClass(NonFiniteValueException::class)]
#[CoversClass(NegativeRadiusException::class)]
#[CoversMethod(Circle::class, '__construct')]
final class ConstructorTest extends TestCase
{
    public function testStoresCenterAndRadius(): void
    {
        // Arrange
        $center = new Point(1, 2);

        // Act
        $circle = new Circle($center, 3.0);

        // Assert
        self::assertSame($center, $circle->center);
        self::assertSame(3.0, $circle->radius);
    }

    public function testAllowsZeroRadius(): void
    {
        // Arrange
        $center = new Point(0, 0);

        // Act
        $circle = new Circle($center, 0.0);

        // Assert
        self::assertSame(0.0, $circle->radius);
    }

    public function testThrowsOnNegativeRadius(): void
    {
        // Arrange
        $center = new Point(0, 0);
        $this->expectException(NegativeRadiusException::class);

        // Act
        new Circle($center, -1.0);
    }

    public function testThrowsOnNonFiniteRadius(): void
    {
        // Arrange
        $center = new Point(0, 0);
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Circle($center, INF);
    }
}
