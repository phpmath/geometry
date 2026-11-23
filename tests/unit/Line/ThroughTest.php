<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Line;

use PHPMath\Geometry\Exception\DegenerateLineException;
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const M_PI;

#[CoversClass(Line::class)]
#[CoversClass(DegenerateLineException::class)]
#[CoversMethod(Line::class, 'through')]
final class ThroughTest extends TestCase
{
    public function testCreatesLineWithAngleBetweenPoints(): void
    {
        // Arrange

        // Act
        $line = Line::through(new Point(1, 1), new Point(2, 2));

        // Assert
        self::assertEqualsWithDelta(M_PI / 4, $line->angle, 1e-9);
    }

    public function testThrowsOnIdenticalPoints(): void
    {
        // Arrange
        $point = new Point(1, 1);
        $this->expectException(DegenerateLineException::class);

        // Act
        Line::through($point, $point);
    }

    public function testAcceptsVeryCloseButDistinctPoints(): void
    {
        // Arrange
        $a = new Point(0, 0);
        $b = new Point(1e-12, 0);

        // Act
        $line = Line::through($a, $b);

        // Assert
        self::assertSame(0.0, $line->angle);
    }
}
