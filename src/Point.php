<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Exception\NonFiniteValueException;

use function hypot;
use function is_finite;

/**
 * An immutable point in the two dimensional plane.
 */
final readonly class Point
{
    public const float DEFAULT_TOLERANCE = 1e-9;

    public function __construct(public float $x, public float $y)
    {
        if (!is_finite($x)) {
            throw NonFiniteValueException::forValue('x coordinate', $x);
        }

        if (!is_finite($y)) {
            throw NonFiniteValueException::forValue('y coordinate', $y);
        }
    }

    public function distanceTo(self $other): float
    {
        return hypot($other->x - $this->x, $other->y - $this->y);
    }

    public function midpointTo(self $other): self
    {
        return new self(($this->x + $other->x) / 2, ($this->y + $other->y) / 2);
    }

    public function translate(float $dx, float $dy): self
    {
        return new self($this->x + $dx, $this->y + $dy);
    }

    /**
     * Whether the distance between both points is at most the given absolute tolerance.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function equals(self $other, float $tolerance = self::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        return $this->distanceTo($other) <= $tolerance;
    }
}
