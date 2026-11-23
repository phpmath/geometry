<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Exception\NegativeRadiusException;
use PHPMath\Geometry\Exception\NonFiniteValueException;

use function abs;
use function is_finite;
use function max;
use function sqrt;

use const M_PI;

/**
 * An immutable circle. A radius of zero is allowed.
 */
final readonly class Circle
{
    public function __construct(public Point $center, public float $radius)
    {
        if (!is_finite($radius)) {
            throw NonFiniteValueException::forValue('radius', $radius);
        }

        if ($radius < 0) {
            throw NegativeRadiusException::forRadius($radius);
        }
    }

    public function area(): float
    {
        return M_PI * $this->radius ** 2;
    }

    public function circumference(): float
    {
        return 2 * M_PI * $this->radius;
    }

    /**
     * Whether the point is inside or on the circle, within the given absolute tolerance
     * (the tolerance is added to the radius).
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        return $this->center->distanceTo($point) <= $this->radius + $tolerance;
    }

    /**
     * The points shared by the outlines of both circles: none, one (tangent) or two.
     *
     * Concentric circles, including identical ones, return an empty list. The absolute tolerance
     * is a distance: circles that miss each other (or are inside each other) by at most that
     * distance count as tangent, and centers at most that far apart count as concentric.
     *
     * @return list<Point>
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function intersectionsWith(self $other, float $tolerance = Point::DEFAULT_TOLERANCE): array
    {
        Tolerance::assertValid($tolerance);

        $distance = $this->center->distanceTo($other->center);

        if (
            $distance <= $tolerance
            || $distance > $this->radius + $other->radius + $tolerance
            || $distance < abs($this->radius - $other->radius) - $tolerance
        ) {
            return [];
        }

        $a = ($this->radius ** 2 - $other->radius ** 2 + $distance ** 2) / (2 * $distance);
        $h = sqrt(max(0.0, $this->radius ** 2 - $a ** 2));

        $ux = ($other->center->x - $this->center->x) / $distance;
        $uy = ($other->center->y - $this->center->y) / $distance;
        $mx = $this->center->x + $a * $ux;
        $my = $this->center->y + $a * $uy;

        if ($h <= $tolerance) {
            return [new Point($mx, $my)];
        }

        return [
            new Point($mx - $h * $uy, $my + $h * $ux),
            new Point($mx + $h * $uy, $my - $h * $ux),
        ];
    }
}
