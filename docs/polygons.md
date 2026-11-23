---
title: Polygons
intro: Area, perimeter, centroid, containment, convexity and orientation of polygons.
group: Guides
order: 60
---

## Creating a polygon

A `Polygon` is a flat shape with straight sides, given as a list of its corner points (vertices) in order. The last point connects back to the first, so you do not repeat the first point. A polygon needs at least three points, otherwise [`TooFewPointsException`](errors.md) is thrown.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$square = new Polygon([
    new Point(0, 0),
    new Point(4, 0),
    new Point(4, 4),
    new Point(0, 4),
]);

echo count($square->points); // 4
```

The array may have any keys. The constructor re-indexes it with `array_values()`, so `$polygon->points` is always a list and no method can fail on a missing key. Every value must be a `Point`. Anything else throws [`InvalidPointException`](errors.md) with a message that names the position of the first bad value. The constructor does not check whether the sides cross each other: a polygon whose sides cross itself (like a bow tie) is accepted, but its results are not meaningful.

```php
use PHPMath\Geometry\Exception\InvalidPointException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$polygon = new Polygon(['a' => new Point(0, 0), 7 => new Point(4, 0), 3 => new Point(0, 4)]);

echo array_key_last($polygon->points); // 2
echo $polygon->area();                  // 8

try {
    new Polygon([new Point(0, 0), 'not a point', new Point(0, 4)]);
} catch (InvalidPointException $exception) {
    echo $exception->getMessage(); // The polygon point at position 1 must be an instance of PHPMath\Geometry\Point, "string" given.
}
```

## Area and perimeter

`area()` returns the area, always as a positive number, using the shoelace formula. `perimeter()` returns the total length of all sides, including the closing side.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$square = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

echo $square->area();      // 16
echo $square->perimeter(); // 16
```

## Centroid

`centroid()` returns the center of mass of the polygon. For a triangle it is the point where the three medians meet.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$triangle = new Polygon([new Point(0, 0), new Point(6, 0), new Point(0, 3)]);

$centroid = $triangle->centroid();

echo $centroid->x, ', ', $centroid->y; // 2, 1
```

A polygon with zero area, such as three points on one line, has no centroid. The method throws [`DegeneratePolygonException`](errors.md).

```php
use PHPMath\Geometry\Exception\DegeneratePolygonException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$flat = new Polygon([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

try {
    $flat->centroid();
} catch (DegeneratePolygonException $exception) {
    echo $exception->getMessage(); // The operation is not defined for a polygon with zero area.
}
```

The zero area check is exact on purpose, so a tiny but real polygon is never rejected. The same exact check decides `orientation()`. The centroid of a triangle with sides of `3.0E-6` is found without trouble.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$tiny = new Polygon([new Point(0, 0), new Point(3e-6, 0), new Point(0, 3e-6)]);

echo $tiny->centroid()->x; // 1.0E-6
```

> [!NOTE]
> The centroid is calculated relative to the first vertex, so the position of the polygon does not matter, and a polygon far from the origin is as accurate as one at the origin. What matters is the shape. The area and the centroid come from sums of cross products of size about `D * D`, where `D` is the largest distance between the first vertex and any other vertex. Each product carries a rounding error of about `1.1E-16` of its size, so the relative error of the centroid is roughly `1.1E-16 * D * D / area`. You keep about `16 + log10(area / (D * D))` correct digits: a polygon whose area is `1.0E-8` of `D * D` keeps about 8 digits, and one whose area is below about `1.0E-16` of `D * D` has no reliable digits left. Only a polygon with exactly zero area throws. Coordinates are also stored as floats, so a flatness smaller than the spacing between neighbouring floats at your coordinates (about `1.5E-8` at `1.0E+8`) cannot be expressed in the first place, and such a polygon is exactly flat.

## Does a point lie in a polygon

`contains()` tests whether a point is inside the polygon, using ray casting: it counts how often a ray from the point crosses the sides. Points on the boundary count as inside. The optional tolerance is a distance: a point at most that far outside an edge still counts, and `0.0` asks for an exact boundary test. The default is `Point::DEFAULT_TOLERANCE`. The ray casting itself is exact, so the tolerance only widens the boundary. An invalid tolerance throws [`InvalidToleranceException`](errors.md).

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$square = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

var_dump($square->contains(new Point(1, 1))); // bool(true)
var_dump($square->contains(new Point(5, 1))); // bool(false)
var_dump($square->contains(new Point(4, 2))); // bool(true)
var_dump($square->contains(new Point(4, 0))); // bool(true)
```

The last two points lie on a side and on a corner. A point just outside a side counts only with a tolerance.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$square = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);

var_dump($square->contains(new Point(4.05, 2)));      // bool(false)
var_dump($square->contains(new Point(4.05, 2), 0.1)); // bool(true)
```

## Convex polygons

A polygon is convex when it has no dents: walking along the sides, you always turn the same way. `isConvex()` tests this. Corners where three points are on one line (collinear points) are allowed, so they do not break convexity. A polygon without area is not convex.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$square = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);
$dented = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(2, 1), new Point(0, 4)]);
$collinear = new Polygon([new Point(0, 0), new Point(2, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);
$flat = new Polygon([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

var_dump($square->isConvex());    // bool(true)
var_dump($dented->isConvex());    // bool(false)
var_dump($collinear->isConvex()); // bool(true)
var_dump($flat->isConvex());      // bool(false)
```

A vertex counts as a straight line when its distance from the line through the two vertices before it is at most the tolerance. The tolerance is an absolute distance, `Point::DEFAULT_TOLERANCE` by default, and it does not scale with the size of the polygon. A small dent can therefore be accepted by passing a larger tolerance, and a polygon smaller than the tolerance has no corners at all, so it is not convex unless you pass a smaller tolerance.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$dent = new Polygon([new Point(0, 0), new Point(2, 0.05), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);
$tiny = new Polygon([new Point(0, 0), new Point(1e-12, 0), new Point(0, 1e-12)]);

var_dump($dent->isConvex());      // bool(false)
var_dump($dent->isConvex(0.2));   // bool(true)
var_dump($tiny->isConvex());      // bool(false)
var_dump($tiny->isConvex(0.0));   // bool(true)
```

An invalid tolerance throws [`InvalidToleranceException`](errors.md). See [Core concepts](concepts.md) for the policy behind all tolerances.

## Orientation

The orientation of a polygon is the direction in which its points go around: clockwise or counter-clockwise. `orientation()` returns the `Orientation` enum. It assumes the y axis points up, as in the usual mathematical plane. On a screen where y points down, the directions look reversed.

```php
use PHPMath\Geometry\Orientation;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$counterClockwise = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);
$clockwise = new Polygon([new Point(0, 0), new Point(0, 4), new Point(4, 4), new Point(4, 0)]);
$flat = new Polygon([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

var_dump($counterClockwise->orientation() === Orientation::CounterClockwise); // bool(true)
var_dump($clockwise->orientation() === Orientation::Clockwise);               // bool(true)
var_dump($flat->orientation() === Orientation::Degenerate);                   // bool(true)

echo $counterClockwise->orientation()->name; // CounterClockwise
```

| Orientation case | Meaning |
|---|---|
| `Orientation::Clockwise` | The signed area is negative. |
| `Orientation::CounterClockwise` | The signed area is positive. |
| `Orientation::Degenerate` | The area is exactly zero. A very small area is not degenerate. |

`area()` is the same for both directions, so reversing the points does not change it.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

$clockwise = new Polygon([new Point(0, 0), new Point(0, 4), new Point(4, 4), new Point(4, 0)]);

echo $clockwise->area(); // 16
```
