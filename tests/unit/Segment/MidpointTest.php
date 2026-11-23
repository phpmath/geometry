<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Segment;

use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Segment::class)]
#[CoversMethod(Segment::class, 'midpoint')]
final class MidpointTest extends TestCase
{
    public function testReturnsCenter(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 2));

        // Act
        $midpoint = $segment->midpoint();

        // Assert
        self::assertSame(2.0, $midpoint->x);
        self::assertSame(1.0, $midpoint->y);
    }
}
