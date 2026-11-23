<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Rectangle;

use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Rectangle::class)]
#[CoversMethod(Rectangle::class, 'perimeter')]
final class PerimeterTest extends TestCase
{
    public function testReturnsTwiceWidthPlusHeight(): void
    {
        // Arrange
        $rectangle = new Rectangle(new Point(0, 0), 3, 4);

        // Act
        $perimeter = $rectangle->perimeter();

        // Assert
        self::assertSame(14.0, $perimeter);
    }
}
