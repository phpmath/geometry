---
title: Points and segments
intro: Measure distances, find midpoints, compare with a tolerance and intersect segments.
group: Guides
order: 30
---

## Points

A `Point` is a position in the plane with an `x` and a `y` coordinate. It is the building block of every other shape.

```php
use PHPMath\Geometry\Point;

$a = new Point(0, 0);
$b = new Point(3, 4);
```

## Distance and midpoint

`distanceTo()` returns the straight line distance between two points. `midpointTo()` returns the point exactly halfway between them.

```php
use PHPMath\Geometry\Point;

$a = new Point(0, 0);
$b = new Point(3, 4);

echo $a->distanceTo($b); // 5

$middle = $a->midpointTo($b);

echo $middle->x, ', ', $middle->y; // 1.5, 2
```

## Moving a point

`translate()` shifts a point by an amount on each axis. It returns a new point.

```php
use PHPMath\Geometry\Point;

$moved = (new Point(3, 4))->translate(-1, 2.5);

echo $moved->x, ', ', $moved->y; // 2, 6.5
```

## Comparing points with a tolerance

`equals()` tests whether two points are the same, within a tolerance. The points count as equal when the distance between them is at most the tolerance. Use it instead of comparing coordinates with `===`, because float arithmetic often leaves tiny errors.

```php
use PHPMath\Geometry\Point;

var_dump((new Point(0.1 + 0.2, 0))->equals(new Point(0.3, 0)));  // bool(true)
var_dump((new Point(1, 1))->equals(new Point(1.05, 1)));         // bool(false)
var_dump((new Point(1, 1))->equals(new Point(1.05, 1), 0.1));    // bool(true)
```

The default tolerance is the constant `Point::DEFAULT_TOLERANCE`.

```php
use PHPMath\Geometry\Point;

var_dump(Point::DEFAULT_TOLERANCE); // float(1.0E-9)
```

The tolerance is the straight line distance between the points, not a limit per coordinate. A point that is off by `0.08` on both axes is about `0.113` away, so a tolerance of `0.1` rejects it.

```php
use PHPMath\Geometry\Point;

var_dump((new Point(0, 0))->equals(new Point(0.08, 0.08), 0.1)); // bool(false)
```

> [!NOTE]
> The tolerance is absolute and must not be negative, `NAN` or infinite, otherwise [`InvalidToleranceException`](errors.md) is thrown. The same rules apply to every method on this page that takes a tolerance. See [Core concepts](concepts.md) for the full policy.

## Segments

A `Segment` is the part of a line between two points. The two points must be different. Identical points throw [`DegenerateSegmentException`](errors.md). This check is exact, with no tolerance: points that are only very close together make a valid, tiny segment.

```php
use PHPMath\Geometry\Exception\DegenerateSegmentException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$segment = new Segment(new Point(0, 0), new Point(3, 4));

echo $segment->length(); // 5

try {
    new Segment(new Point(1, 1), new Point(1, 1));
} catch (DegenerateSegmentException $exception) {
    echo $exception->getMessage(); // A segment requires two distinct points.
}
```

`midpoint()` returns the point halfway between the start and the end.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$middle = (new Segment(new Point(0, 0), new Point(3, 4)))->midpoint();

echo $middle->x, ', ', $middle->y; // 1.5, 2
```

## Does a point lie on a segment

`contains()` tests whether a point lies on the segment. The point counts when its distance to the closest spot of the segment is at most the tolerance. The end points belong to the segment.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$segment = new Segment(new Point(0, 0), new Point(3, 4));

var_dump($segment->contains(new Point(1.5, 2)));         // bool(true)
var_dump($segment->contains(new Point(6, 8)));           // bool(false)
var_dump($segment->contains(new Point(1.5, 2.05)));      // bool(false)
var_dump($segment->contains(new Point(1.5, 2.05), 0.1)); // bool(true)
```

Here `(6, 8)` is on the same infinite line, but past the end of the segment.

## Intersecting segments

`intersectionWith()` returns the point where two segments cross, or `null` when they do not touch. The result is a `Point` or `null`, so always check it. The optional tolerance is a distance: a crossing that lies at most that far beyond an end point still counts.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$segment = new Segment(new Point(0, 0), new Point(3, 4));

$cross = $segment->intersectionWith(new Segment(new Point(0, 4), new Point(3, 0)));

echo $cross->x, ', ', $cross->y; // 1.5, 2

$apart = $segment->intersectionWith(new Segment(new Point(10, 0), new Point(10, 5)));

var_dump($apart); // NULL
```

Segments that only touch at an end point do intersect.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$a = new Segment(new Point(0, 0), new Point(3, 4));
$b = new Segment(new Point(3, 4), new Point(6, 0));

$touch = $a->intersectionWith($b);

echo $touch->x, ', ', $touch->y; // 3, 4
```

Parallel segments never have a single shared point, so they return `null`. This includes two segments on the same line that overlap.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$segment = new Segment(new Point(0, 0), new Point(3, 4));
$overlap = new Segment(new Point(1.5, 2), new Point(6, 8));

var_dump($segment->intersectionWith($overlap)); // NULL
```

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Segment;

$a = new Segment(new Point(0, 0), new Point(4, 0));
$b = new Segment(new Point(4.05, -1), new Point(4.05, 1));

var_dump($a->intersectionWith($b));                   // NULL
echo $a->intersectionWith($b, 0.1)->x;                // 4.05
```

The test for parallel segments is different on purpose. It is relative, because whether two segments are parallel depends on the angle between them and not on their size: the cross product of the directions must exceed `1e-12` times the product of both lengths. It does not use the tolerance and cannot be changed.

> [!WARNING]
> A `null` result does not always mean the segments are apart. Two overlapping segments on the same line also return `null`. Use `contains()` on the end points if you need to detect an overlap.
