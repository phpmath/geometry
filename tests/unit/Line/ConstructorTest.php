<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Line;

use PHPMath\Geometry\Exception\NonFiniteValueException;
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const NAN;

#[CoversClass(Line::class)]
#[CoversClass(NonFiniteValueException::class)]
#[CoversMethod(Line::class, '__construct')]
final class ConstructorTest extends TestCase
{
    public function testStoresPointAndAngle(): void
    {
        // Arrange
        $point = new Point(1, 2);

        // Act
        $line = new Line($point, 0.5);

        // Assert
        self::assertSame($point, $line->point);
        self::assertSame(0.5, $line->angle);
    }

    public function testThrowsOnNonFiniteAngle(): void
    {
        // Arrange
        $point = new Point(1, 2);
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Line($point, NAN);
    }
}
