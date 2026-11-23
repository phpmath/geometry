---
title: Core concepts
intro: The few ideas you need to use the geometry library well.
group: Getting started
order: 20
---

## Shapes are immutable

Every class is `final readonly`. Once created, a shape never changes. Methods such as `translate()` return a new instance and leave the original untouched, so you can pass shapes around without defensive copies.

```php
use PHPMath\Geometry\Point;

$start = new Point(1, 2);
$moved = $start->translate(3, 4);

echo $start->x, ', ', $start->y, "\n"; // 1, 2
echo $moved->x, ', ', $moved->y, "\n"; // 4, 6
```

Trying to assign to a property fails, because the properties are `readonly`.

```php
use PHPMath\Geometry\Point;

$point = new Point(1, 2);

try {
    $point->x = 5;
} catch (Error $exception) {
    echo $exception->getMessage(); // Cannot modify readonly property PHPMath\Geometry\Point::$x
}
```

## Creating shapes

Shapes are created with their constructor. A few classes also have a named constructor: `Line::through()` and `Rectangle::fromCorners()`. Shapes are built from each other, so a circle takes a `Point` for its center.

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;

$circle = new Circle(new Point(1, 1), 2.5);

echo $circle->center->x; // 1
echo $circle->radius;    // 2.5
```

All numbers are `float`. PHP converts an `int` to a float when you pass one, so `new Point(3, 4)` works. Methods that combine two shapes take the shape itself, not an array or a raw number.

> [!NOTE]
> The library uses the usual mathematical plane. The x axis points right, the y axis points up and angles are in radians, measured counter-clockwise from the positive x axis.

## Reading values

Every value you passed to a constructor is available as a public read-only property. Calculated values come from methods, for example `area()` or `length()`.

```php
use PHPMath\Geometry\Point;

$point = new Point(3, 4);

var_dump($point->x); // float(3)
```

Methods return plain `float`, `bool`, `null`, a `Point`, a list of points or an `Orientation` enum. There is no `__toString()`, so a shape cannot be echoed by accident.

## Floats and tolerance

Floats cannot store every decimal exactly, so `0.1 + 0.2` is not exactly `0.3`. One policy covers every method that compares floats: it takes an optional `float $tolerance`, an absolute value, and the default is `Point::DEFAULT_TOLERANCE`, which is `1e-9`. Absolute means that it does not grow or shrink with the size of your shapes. Coordinates are in whatever unit you choose, so the tolerance is in that same unit.

```php
use PHPMath\Geometry\Point;

var_dump(0.1 + 0.2 === 0.3);                                    // bool(false)
var_dump((new Point(0.1 + 0.2, 0))->equals(new Point(0.3, 0))); // bool(true)
```

The tolerance has the same meaning everywhere: it is a distance, measured the Euclidean way (straight line), and a value exactly at the tolerance still counts. The two line methods are the one exception, because lines have no length: there it is the sine of an angle, a number without unit.

| Method | What the tolerance measures |
|---|---|
| `Point::equals()` | The distance between the two points. |
| `Segment::contains()` | The distance from the point to the closest spot of the segment. |
| `Segment::intersectionWith()` | How far beyond an end point a crossing may lie. The test for parallel segments is relative, see below. |
| `Circle::contains()` | How far outside the edge a point may lie. |
| `Circle::intersectionsWith()` | The gap between circles that still count as touching, and the distance between centers that still counts as concentric. |
| `Rectangle::contains()` | The distance from the point to the rectangle. |
| `Polygon::contains()` | The distance from the point to an edge. |
| `Polygon::isConvex()` | The distance of a vertex from the line through the two vertices before it. |
| `Line::isParallelTo()` and `Line::intersectionWith()` | The sine of the angle between the lines (for small angles roughly the angle in radians). |

A tolerance that is negative, `NAN` or infinite makes no sense, so every one of these methods throws [`InvalidToleranceException`](errors.md) for it, whether or not the tolerance would have been used in that call. A tolerance of `0.0` is valid and asks for an exact comparison.

```php
use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Point;

try {
    (new Point(0, 0))->equals(new Point(1, 1), -0.5);
} catch (InvalidToleranceException $exception) {
    echo $exception->getMessage(); // The tolerance must be a finite number that is not negative, "-0.5" given.
}
```

Because the tolerance is absolute, choose it to fit your data. For shapes much smaller than `1e-9`, every point counts as equal to every other with the default, so pass a smaller value. For coordinates around `1e7` or larger, neighbouring floats are further apart than `1e-9`, so only a larger tolerance can absorb rounding errors.

`Segment::intersectionWith()` has a second, relative limit on purpose. Whether two segments are parallel depends on the angle between them, not on their size, so the cross product of the directions must exceed `1e-12` times the product of both lengths. An absolute limit would call small segments parallel and treat long, truly crossing segments as non parallel. This limit is not configurable. See [Points and segments](points-and-segments.md).

Boundaries are inclusive. A point exactly on the edge of a circle, rectangle or polygon counts as inside.

## Exact checks

Some checks are exact, with no tolerance, on purpose. They reject only input that is truly degenerate, so a tiny but real shape is never refused:

- `new Segment()` and `Line::through()` reject two points that have exactly the same coordinates. Points that are only very close together are accepted.
- `Polygon::centroid()` throws only when the area is exactly `0.0`, and `Polygon::orientation()` returns `Orientation::Degenerate` only for an area of exactly `0.0`.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPMath\Geometry\Segment;

$tiny = new Segment(new Point(0, 0), new Point(1e-12, 0));
$triangle = new Polygon([new Point(0, 0), new Point(1e-9, 0), new Point(0, 1e-9)]);

echo $tiny->length();                   // 1.0E-12
echo $triangle->orientation()->name;    // CounterClockwise
```

## NAN and infinity

A shape can never hold `NAN` or an infinite value. Every constructor checks its float arguments and throws [`NonFiniteValueException`](errors.md). Because of this, the calculations inside the library start from finite numbers. A result can still overflow to infinity when the input is near the limit of a float, for example a coordinate close to `1.0E+308`. In that case the next shape you try to build from the result throws.

A `NAN` or infinite tolerance is rejected with [`InvalidToleranceException`](errors.md), see above.

## Errors

Invalid input throws a specific exception from the `PHPMath\Geometry\Exception` namespace. Every one of them implements the `GeometryException` marker interface. The exceptions are thrown by constructors, named constructors, `centroid()` and the methods that take a tolerance. All other methods accept any valid shape and always return a result. See [Errors and exceptions](errors.md).

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Exception\NegativeRadiusException;
use PHPMath\Geometry\Point;

try {
    new Circle(new Point(0, 0), -1);
} catch (NegativeRadiusException $exception) {
    echo $exception->getMessage(); // The radius must not be negative, "-1" given.
}
```

## Enums for results

One method returns an enum instead of a magic number: `Polygon::orientation()` returns `Orientation`. See [Polygons](polygons.md).
