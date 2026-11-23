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
#[CoversMethod(Point::class, 'distanceTo')]
final class DistanceToTest extends TestCase
{
    public function testReturnsEuclideanDistance(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(3, 4);

        // Act
        $distance = $a->distanceTo($b);

        // Assert
        self::assertEqualsWithDelta(5.0, $distance, 1e-9);
    }

    public function testReturnsZeroForSamePoint(): void
    {
        // Arrange
        $a = new Point(2, 2);

        // Act
        $distance = $a->distanceTo($a);

        // Assert
        self::assertSame(0.0, $distance);
    }
}
