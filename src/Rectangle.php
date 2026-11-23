<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Exception\NegativeSizeException;
use PHPMath\Geometry\Exception\NonFiniteValueException;

use function abs;
use function hypot;
use function is_finite;
use function max;
use function min;

/**
 * An immutable axis-aligned rectangle, defined by its minimum (bottom-left) corner and its size.
 */
final readonly class Rectangle
{
    public function __construct(public Point $origin, public float $width, public float $height)
    {
        if (!is_finite($width)) {
            throw NonFiniteValueException::forValue('width', $width);
        }

        if (!is_finite($height)) {
            throw NonFiniteValueException::forValue('height', $height);
        }

        if ($width < 0 || $height < 0) {
            throw NegativeSizeException::forSize($width, $height);
        }
    }

    /**
     * Creates the rectangle spanned by two opposite corners, given in any order.
     */
    public static function fromCorners(Point $a, Point $b): self
    {
        return new self(
            new Point(min($a->x, $b->x), min($a->y, $b->y)),
            abs($a->x - $b->x),
            abs($a->y - $b->y),
        );
    }

    public function area(): float
    {
        return $this->width * $this->height;
    }

    public function perimeter(): float
    {
        return 2 * ($this->width + $this->height);
    }

    /**
     * Whether the point is inside or on the edge of the rectangle, or at most the given absolute
     * tolerance outside it. The tolerance is the distance from the point to the rectangle, so it
     * is measured diagonally beyond a corner.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        $dx = max($this->origin->x - $point->x, 0.0, $point->x - ($this->origin->x + $this->width));
        $dy = max($this->origin->y - $point->y, 0.0, $point->y - ($this->origin->y + $this->height));

        return hypot($dx, $dy) <= $tolerance;
    }

    /**
     * Whether both rectangles share at least one point. Touching edges count.
     */
    public function intersects(self $other): bool
    {
        return $this->origin->x <= $other->origin->x + $other->width
            && $other->origin->x <= $this->origin->x + $this->width
            && $this->origin->y <= $other->origin->y + $other->height
            && $other->origin->y <= $this->origin->y + $this->height;
    }
}
