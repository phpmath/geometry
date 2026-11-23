<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Circle;

use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const M_PI;

#[CoversClass(Circle::class)]
#[CoversMethod(Circle::class, 'area')]
final class AreaTest extends TestCase
{
    public function testReturnsPiTimesRadiusSquared(): void
    {
        // Arrange
        $circle = new Circle(new Point(0, 0), 2);

        // Act
        $area = $circle->area();

        // Assert
        self::assertEqualsWithDelta(4 * M_PI, $area, 1e-9);
    }
}
