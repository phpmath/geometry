<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Segment;

use PHPMath\Geometry\Exception\DegenerateSegmentException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Segment::class)]
#[CoversClass(DegenerateSegmentException::class)]
#[CoversMethod(Segment::class, '__construct')]
final class ConstructorTest extends TestCase
{
    public function testStoresEndpoints(): void
    {
        // Arrange
        $start = new Point(0, 0);
        $end = new Point(1, 1);

        // Act
        $segment = new Segment($start, $end);

        // Assert
        self::assertSame($start, $segment->start);
        self::assertSame($end, $segment->end);
    }

    public function testThrowsOnIdenticalPoints(): void
    {
        // Arrange
        $point = new Point(1, 1);
        $this->expectException(DegenerateSegmentException::class);

        // Act
        new Segment($point, $point);
    }

    public function testAcceptsVeryCloseButDistinctPoints(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(1e-12, 0);

        // Act
        $segment = new Segment($a, $b);

        // Assert
        self::assertSame($b, $segment->end);
    }
}
