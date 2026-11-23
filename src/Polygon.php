<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\DegeneratePolygonException;
use PHPMath\Geometry\Exception\InvalidPointException;
use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Exception\TooFewPointsException;

use function abs;
use function array_values;
use function count;
use function hypot;

/**
 * An immutable simple polygon, defined by its vertices in order. The closing edge is implicit.
 *
 * Self-intersecting polygons are not detected; their results are not meaningful.
 */
final readonly class Polygon
{
    /** @var list<Point> */
    public array $points;

    /**
     * @param array<array-key, mixed> $points The vertices in order. Any keys are accepted, the points are re-indexed.
     *
     * @throws InvalidPointException When a value is not a Point.
     * @throws TooFewPointsException When there are fewer than three points.
     */
    public function __construct(array $points)
    {
        $validated = [];

        foreach (array_values($points) as $position => $point) {
            if (!$point instanceof Point) {
                throw InvalidPointException::forPosition($position, $point);
            }

            $validated[] = $point;
        }

        if (count($validated) < 3) {
            throw TooFewPointsException::forCount(count($validated));
        }

        $this->points = $validated;
    }

    /**
     * The absolute area, calculated with the shoelace formula.
     */
    public function area(): float
    {
        return abs($this->signedArea());
    }

    public function perimeter(): float
    {
        $perimeter = 0.0;

        foreach ($this->edges() as [$a, $b]) {
            $perimeter += $a->distanceTo($b);
        }

        return $perimeter;
    }

    /**
     * The center of mass of the polygon.
     *
     * The zero area check is exact on purpose: a tiny but real polygon is never rejected. The result
     * loses digits when the area is small compared to the squared size of the polygon (see the
     * polygon guide). The calculation is relative to the first vertex, so the position of the polygon
     * does not matter.
     *
     * @throws DegeneratePolygonException When the polygon has exactly zero area.
     */
    public function centroid(): Point
    {
        $signedArea = $this->signedArea();

        if ($signedArea === 0.0) {
            throw DegeneratePolygonException::forZeroArea();
        }

        $origin = $this->points[0];
        $cx = 0.0;
        $cy = 0.0;

        foreach ($this->edges() as [$a, $b]) {
            $ax = $a->x - $origin->x;
            $ay = $a->y - $origin->y;
            $bx = $b->x - $origin->x;
            $by = $b->y - $origin->y;
            $cross = $ax * $by - $bx * $ay;
            $cx += ($ax + $bx) * $cross;
            $cy += ($ay + $by) * $cross;
        }

        return new Point(
            $origin->x + $cx / (6 * $signedArea),
            $origin->y + $cy / (6 * $signedArea),
        );
    }

    /**
     * Whether the point is inside the polygon (ray casting). Points on the boundary count as inside,
     * and so do points at most the given absolute tolerance (a distance) outside an edge.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        $inside = false;

        foreach ($this->edges() as [$a, $b]) {
            if ($this->isOnEdge($point, $a, $b, $tolerance)) {
                return true;
            }

            if (($a->y > $point->y) === ($b->y > $point->y)) {
                continue;
            }

            $crossingX = ($b->x - $a->x) * ($point->y - $a->y) / ($b->y - $a->y) + $a->x;

            if ($point->x >= $crossingX) {
                continue;
            }

            $inside = !$inside;
        }

        return $inside;
    }

    /**
     * Whether all turns go the same way. Collinear vertices are allowed; a polygon without area is not convex.
     *
     * A vertex counts as collinear when it lies at most the given absolute tolerance (a distance) from
     * the line through the two previous vertices.
     *
     * @throws InvalidToleranceException When the tolerance is negative, NAN or infinite.
     */
    public function isConvex(float $tolerance = Point::DEFAULT_TOLERANCE): bool
    {
        Tolerance::assertValid($tolerance);

        $count = count($this->points);
        $sign = 0;

        for ($i = 0; $i < $count; ++$i) {
            $a = $this->points[$i];
            $b = $this->points[($i + 1) % $count];
            $c = $this->points[($i + 2) % $count];

            $cross = ($b->x - $a->x) * ($c->y - $b->y) - ($b->y - $a->y) * ($c->x - $b->x);

            if (abs($cross) <= $tolerance * hypot($b->x - $a->x, $b->y - $a->y)) {
                continue;
            }

            $current = $cross > 0 ? 1 : -1;

            if ($sign !== 0 && $sign !== $current) {
                return false;
            }

            $sign = $current;
        }

        return $sign !== 0;
    }

    /**
     * The winding direction of the points. The zero area check is exact on purpose, so a tiny but
     * real polygon still has an orientation.
     */
    public function orientation(): Orientation
    {
        $signedArea = $this->signedArea();

        return match (true) {
            $signedArea > 0 => Orientation::CounterClockwise,
            $signedArea < 0 => Orientation::Clockwise,
            default => Orientation::Degenerate,
        };
    }

    private function signedArea(): float
    {
        $origin = $this->points[0];
        $sum = 0.0;

        foreach ($this->edges() as [$a, $b]) {
            $sum += ($a->x - $origin->x) * ($b->y - $origin->y) - ($b->x - $origin->x) * ($a->y - $origin->y);
        }

        return $sum / 2;
    }

    /** @return iterable<array{Point, Point}> */
    private function edges(): iterable
    {
        $count = count($this->points);

        for ($i = 0; $i < $count; ++$i) {
            yield [$this->points[$i], $this->points[($i + 1) % $count]];
        }
    }

    private function isOnEdge(Point $point, Point $a, Point $b, float $tolerance): bool
    {
        if ($a->x === $b->x && $a->y === $b->y) {
            return $point->equals($a, $tolerance);
        }

        return (new Segment($a, $b))->contains($point, $tolerance);
    }
}
