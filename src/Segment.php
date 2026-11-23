<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\DegenerateSegmentException;
use PHPMath\Geometry\Exception\InvalidToleranceException;

use function abs;
use function max;
use function min;

/**
 * An immutable line segment between two distinct points.
 *
 * The check for distinct points is exact on purpose: points that are only very close together
 * are accepted, so a tiny but real segment is never rejected.
 */
final readonly class Segment
{
    public function __construct(public Point $start, public Point $end)
    {
        if ($start->x === $end->x && $start->y === $end->y) {
            throw DegenerateSegmentException::forIdenticalPoints();
        }
    }

    public function length(): float
    {
        return $this->start->distanceTo($this->end);
    }

    public function midpoint(): Point
    {
        return $this->start->midpointTo($this->end);
    }

    /**
     * The single point shared by both segments, or null when they do not touch.
     *
     * Parallel segments, including collinear overlapping ones, return null.
     *
     * Two limits are used. The test for parallel segments is relative and scale aware: the cross product
     * of the directions must exceed 1e-12 times the product of both lengths, which is a sine of about 1e-12
     * whatever the size of the segments (an absolute limit would reject small segments and accept long ones
     * that are not parallel). The test whether the crossing lies on a segment uses the absolute tolerance
     * as a distance, so a crossing at most that far beyond an end point still counts.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function intersectionWith(self $other, float $tolerance = Point::DEFAULT_TOLERANCE): ?Point
    {
        Tolerance::assertValid($tolerance);

        $rx = $this->end->x - $this->start->x;
        $ry = $this->end->y - $this->start->y;
        $sx = $other->end->x - $other->start->x;
        $sy = $other->end->y - $other->start->y;

        $denominator = $rx * $sy - $ry * $sx;

        if (abs($denominator) <= 1e-12 * $this->length() * $other->length()) {
            return null;
        }

        $qpx = $other->start->x - $this->start->x;
        $qpy = $other->start->y - $this->start->y;

        $t = ($qpx * $sy - $qpy * $sx) / $denominator;
        $u = ($qpx * $ry - $qpy * $rx) / $denominator;
        $thisSlack = $tolerance / $this->length();
        $otherSlack = $tolerance / $other->length();

        if ($t < -$thisSlack || $t > 1 + $thisSlack || $u < -$otherSlack || $u > 1 + $otherSlack) {
            return null;
        }

        return new Point($this->start->x + $t * $rx, $this->start->y + $t * $ry);
    }

    /**
     * Whether the point lies on the segment, within the given absolute distance tolerance.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        $dx = $this->end->x - $this->start->x;
        $dy = $this->end->y - $this->start->y;

        $t = (($point->x - $this->start->x) * $dx + ($point->y - $this->start->y) * $dy) / ($dx * $dx + $dy * $dy);
        $t = max(0.0, min(1.0, $t));

        $closest = new Point($this->start->x + $t * $dx, $this->start->y + $t * $dy);

        return $point->distanceTo($closest) <= $tolerance;
    }
}
