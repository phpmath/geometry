<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Segment;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Segment::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Segment::class, 'contains')]
final class ContainsTest extends TestCase
{
    public function testReturnsTrueForPointOnSegment(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 4));

        // Act
        $result = $segment->contains(new Point(2, 2));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForEndpoint(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 4));

        // Act
        $result = $segment->contains(new Point(4, 4));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForCollinearPointBeyondEnd(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 4));

        // Act
        $result = $segment->contains(new Point(5, 5));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForPointOffSegment(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 0));

        // Act
        $result = $segment->contains(new Point(2, 1));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueForNearPointWithinCustomTolerance(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 0));

        // Act
        $result = $segment->contains(new Point(2, 0.05), 0.1);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseOutsideZeroTolerance(): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 0));

        // Act
        $result = $segment->contains(new Point(2, 1e-12), 0.0);

        // Assert
        self::assertFalse($result);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $segment = new Segment(new Point(0, 0), new Point(4, 0));
        $this->expectException(InvalidToleranceException::class);

        // Act
        $segment->contains(new Point(2, 0), $tolerance);
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }
}
