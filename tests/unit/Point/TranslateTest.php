<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Point;

use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Point::class)]
#[CoversMethod(Point::class, 'translate')]
final class TranslateTest extends TestCase
{
    public function testReturnsMovedPoint(): void
    {
        // Arrange
        $point = new Point(1, 1);

        // Act
        $moved = $point->translate(2.0, -3.0);

        // Assert
        self::assertSame(3.0, $moved->x);
        self::assertSame(-2.0, $moved->y);
    }

    public function testDoesNotChangeOriginal(): void
    {
        // Arrange
        $point = new Point(1, 1);

        // Act
        $point->translate(2.0, 2.0);

        // Assert
        self::assertSame(1.0, $point->x);
        self::assertSame(1.0, $point->y);
    }
}
