<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\DegenerateLineException;
use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Exception\NonFiniteValueException;

use function abs;
use function atan2;
use function cos;
use function fmod;
use function hypot;
use function is_finite;
use function max;
use function round;
use function sin;

use const M_PI_4;
use const PHP_FLOAT_EPSILON;

/**
 * An immutable infinite line, defined by a point on it and its angle in radians
 * (measured counter-clockwise from the positive x axis).
 *
 * Internally the line works with a direction vector. For angles that are a multiple of a quarter
 * turn (axis aligned lines) or an eighth turn (diagonals), within a few units of float rounding, the
 * vector is exact: (1, 0), (1, 1), (0, 1) and so on. Calculations with such lines are free of the
 * errors that sine and cosine would introduce. Other angles use cos(angle) and sin(angle).
 *
 * The check for two distinct points in through() is exact on purpose: points that are only very
 * close together define a line, so a tiny but real line is never rejected.
 */
final readonly class Line
{
    /** Exact direction vectors for the multiples of an eighth turn, indexed by the multiple modulo 8. */
    private const array EXACT_DIRECTIONS = [
        [1.0, 0.0],
        [1.0, 1.0],
        [0.0, 1.0],
        [-1.0, 1.0],
        [-1.0, 0.0],
        [-1.0, -1.0],
        [0.0, -1.0],
        [1.0, -1.0],
    ];

    private float $directionX;

    private float $directionY;

    public function __construct(public Point $point, public float $angle)
    {
        if (!is_finite($angle)) {
            throw NonFiniteValueException::forValue('angle', $angle);
        }

        $multiple = round($angle / M_PI_4);

        if (abs($angle - $multiple * M_PI_4) <= 4 * PHP_FLOAT_EPSILON * max(1.0, abs($angle))) {
            $index = (int) (fmod($multiple, 8.0) + 8.0) % 8;
            [$this->directionX, $this->directionY] = self::EXACT_DIRECTIONS[$index];

            return;
        }

        $this->directionX = cos($angle);
        $this->directionY = sin($angle);
    }

    public static function through(Point $a, Point $b): self
    {
        if ($a->x === $b->x && $a->y === $b->y) {
            throw DegenerateLineException::forIdenticalPoints();
        }

        return new self($a, atan2($b->y - $a->y, $b->x - $a->x));
    }

    /**
     * Whether the lines are parallel. The absolute tolerance is compared to the sine of the angle
     * between the lines. Identical lines and lines with opposite directions are parallel.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function isParallelTo(self $other, float $tolerance = Point::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        return abs($this->sineTo($other)) <= $tolerance;
    }

    /**
     * The point where both lines meet, or null when they are parallel (or identical).
     *
     * The lines count as parallel when the sine of the angle between them is at most the absolute
     * tolerance. The crossing is calculated from the direction vectors, so it is exact for axis
     * aligned and diagonal lines through simple points.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function intersectionWith(self $other, float $tolerance = Point::DEFAULT_TOLERANCE): ?Point
    {
        Tolerance::assertValid($tolerance);

        $cross = $this->cross($other);

        if (abs($this->sineTo($other)) <= $tolerance) {
            return null;
        }

        $dx = $other->point->x - $this->point->x;
        $dy = $other->point->y - $this->point->y;
        $t = ($dx * $other->directionY - $dy * $other->directionX) / $cross;

        return new Point(
            $this->point->x + $t * $this->directionX + 0.0,
            $this->point->y + $t * $this->directionY + 0.0,
        );
    }

    public function distanceTo(Point $point): float
    {
        $dx = $point->x - $this->point->x;
        $dy = $point->y - $this->point->y;

        return abs($dx * $this->directionY - $dy * $this->directionX) / $this->norm();
    }

    private function cross(self $other): float
    {
        return $this->directionX * $other->directionY - $this->directionY * $other->directionX;
    }

    private function norm(): float
    {
        return hypot($this->directionX, $this->directionY);
    }

    private function sineTo(self $other): float
    {
        return $this->cross($other) / ($this->norm() * $other->norm());
    }
}
