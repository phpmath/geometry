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
#[CoversMethod(Segment::class, 'length')]
final class LengthTest extends TestCase
{
    public function testReturnsDistanceBetweenEndpoints(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(3, 4));

        // Act
        $length = $segment->length();

        // Assert
        self::assertEqualsWithDelta(5.0, $length, 1e-9);
    }
}
