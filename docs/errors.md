---
title: Errors and exceptions
intro: What can go wrong and how to catch it.
group: Reference
order: 80
---

## Library exceptions

The library throws nine exceptions, all in the `PHPMath\Geometry\Exception` namespace. Every one of them is a `final` class that implements the marker interface `GeometryException`, so a single `catch (GeometryException $exception)` handles every error the library raises on purpose. Each exception also extends one standard PHP exception that says what kind of problem it is: `InvalidArgumentException` for an argument with an invalid value, and `DomainException` for a valid argument whose result is mathematically undefined.

| Exception | Extends | Thrown when |
|---|---|---|
| `NonFiniteValueException` | `InvalidArgumentException` | A coordinate, angle, radius, width or height is `NAN` or infinite. |
| `NegativeRadiusException` | `InvalidArgumentException` | A circle is created with a negative radius. |
| `NegativeSizeException` | `InvalidArgumentException` | A rectangle is created with a negative width or height. |
| `DegenerateSegmentException` | `InvalidArgumentException` | A segment is created with identical start and end points. |
| `DegenerateLineException` | `InvalidArgumentException` | `Line::through()` gets two identical points. |
| `TooFewPointsException` | `InvalidArgumentException` | A polygon is created with fewer than three points. |
| `InvalidPointException` | `InvalidArgumentException` | A polygon is created with a value that is not a `Point`. |
| `InvalidToleranceException` | `InvalidArgumentException` | A tolerance is negative, `NAN` or infinite. |
| `DegeneratePolygonException` | `DomainException` | `centroid()` is called on a polygon with zero area. |

## When each exception is thrown

This table lists every case where a method rejects its input. A method that is not in the table never throws a library exception.

| Method | Exception | Condition |
|---|---|---|
| `new Point()` | `NonFiniteValueException` | `$x` or `$y` is `NAN` or infinite. |
| `new Segment()` | `DegenerateSegmentException` | `$start` and `$end` have exactly the same coordinates. |
| `new Line()` | `NonFiniteValueException` | `$angle` is `NAN` or infinite. |
| `Line::through()` | `DegenerateLineException` | Both points have exactly the same coordinates. |
| `new Circle()` | `NonFiniteValueException` | `$radius` is `NAN` or infinite. |
| `new Circle()` | `NegativeRadiusException` | `$radius` is below zero. |
| `new Rectangle()` | `NonFiniteValueException` | `$width` or `$height` is `NAN` or infinite. |
| `new Rectangle()` | `NegativeSizeException` | `$width` or `$height` is below zero. |
| `new Polygon()` | `InvalidPointException` | A value in the array is not a `Point`. The message names its position. |
| `new Polygon()` | `TooFewPointsException` | There are fewer than three points. |
| `Polygon::centroid()` | `DegeneratePolygonException` | The signed area is exactly zero. |
| `Point::equals()`, `Segment::contains()`, `Segment::intersectionWith()`, `Circle::contains()`, `Circle::intersectionsWith()`, `Rectangle::contains()`, `Polygon::contains()`, `Polygon::isConvex()`, `Line::isParallelTo()`, `Line::intersectionWith()` | `InvalidToleranceException` | `$tolerance` is negative, `NAN` or infinite. |

Methods that build a new point, such as `translate()` and `midpointTo()`, call the `Point` constructor. They throw `NonFiniteValueException` when the result overflows to infinity, for example when you translate a coordinate near `1.0E+308` by the same amount. `Rectangle::fromCorners()` calls the `Rectangle` constructor in the same way.

Identical points are compared exactly, not with a tolerance, and so is the zero area in `Polygon::centroid()`. Two points that are only very close together are accepted by `Segment` and `Line::through()`, and a tiny polygon has a centroid. See [Core concepts](concepts.md) for the tolerance policy.

When a polygon is created, the keys of the array do not matter (the points are re-indexed), but every value must be a `Point`. The message names the position of the first value that is not.

```php
use PHPMath\Geometry\Exception\InvalidPointException;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;

try {
    new Polygon([new Point(0, 0), [1, 1], new Point(1, 0)]);
} catch (InvalidPointException $exception) {
    echo $exception->getMessage(); // The polygon point at position 1 must be an instance of PHPMath\Geometry\Point, "array" given.
}
```

## Handling invalid input

Catch the specific exception when you want to react to one case, or the `GeometryException` marker interface to handle every error from this library in one place.

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Exception\GeometryException;
use PHPMath\Geometry\Point;

try {
    $circle = new Circle(new Point(0, 0), -2.0);
} catch (GeometryException $exception) {
    echo $exception::class . ': ' . $exception->getMessage();
    // PHPMath\Geometry\Exception\NegativeRadiusException: The radius must not be negative, "-2" given.
}
```
