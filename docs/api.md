---
title: API reference
intro: Every public class, constructor and method of the geometry library.
group: Reference
order: 70
layout: reference
---

Every class is `final readonly` and lives in the `PHPMath\Geometry` namespace. Methods never change the object they are called on. All numbers are `float`. Methods that take a tolerance default to `Point::DEFAULT_TOLERANCE` (`1e-9`), an absolute value that must not be negative, `NAN` or infinite. The policy is explained in [Core concepts](concepts.md).

## Point

An immutable point in the plane. It has the read-only properties `float $x` and `float $y`, and the constant `Point::DEFAULT_TOLERANCE` with the value `1e-9`.

### __construct()

```php
public function __construct(public float $x, public float $y)
```

Creates a point.

- **Parameters:** `$x` the x coordinate, `$y` the y coordinate
- **Throws:** [NonFiniteValueException](errors.md) if a coordinate is `NAN` or infinite

### distanceTo()

```php
public function distanceTo(Point $other): float
```

Returns the straight line distance to another point.

- **Parameters:** `$other` the other point
- **Returns:** `float`

### midpointTo()

```php
public function midpointTo(Point $other): self
```

Returns the point halfway between this point and another one.

- **Parameters:** `$other` the other point
- **Returns:** `Point`
- **Throws:** [NonFiniteValueException](errors.md) if the sum of the coordinates overflows to infinity

### translate()

```php
public function translate(float $dx, float $dy): self
```

Returns a copy of the point moved by the given amounts.

- **Parameters:** `$dx` the shift along the x axis, `$dy` the shift along the y axis
- **Returns:** `Point`
- **Throws:** [NonFiniteValueException](errors.md) if `$dx` or `$dy` is `NAN` or infinite, or the result overflows

### equals()

```php
public function equals(Point $other, float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether the distance between both points is at most the tolerance.

- **Parameters:** `$other` the other point, `$tolerance` the largest allowed distance
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

## Segment

An immutable line segment between two distinct points. It has the read-only properties `Point $start` and `Point $end`.

### __construct()

```php
public function __construct(public Point $start, public Point $end)
```

Creates a segment.

- **Parameters:** `$start` the first end point, `$end` the second end point
- **Throws:** [DegenerateSegmentException](errors.md) if both points have exactly the same coordinates

### length()

```php
public function length(): float
```

Returns the distance between the end points.

- **Returns:** `float`

### midpoint()

```php
public function midpoint(): Point
```

Returns the point halfway between the end points.

- **Returns:** `Point`

### intersectionWith()

```php
public function intersectionWith(Segment $other, float $tolerance = Point::DEFAULT_TOLERANCE): ?Point
```

Returns the single point shared by both segments. Parallel segments return `null`, including overlapping segments on the same line. Segments that touch at an end point do intersect. The tolerance is a distance: a crossing at most that far beyond an end point still counts. The test for parallel segments is relative and not configurable: the cross product of the directions must exceed `1e-12` times the product of both lengths, so it depends on the angle and not on the size of the segments.

- **Parameters:** `$other` the other segment, `$tolerance` the largest allowed distance beyond an end point
- **Returns:** `Point` or `null` when the segments do not touch or are parallel
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

### contains()

```php
public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether a point lies on the segment, within a distance tolerance.

- **Parameters:** `$point` the point to test, `$tolerance` the largest allowed distance to the segment
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

## Line

An immutable infinite line, defined by a point on the line and an angle in radians, measured counter-clockwise from the positive x axis. It has the read-only properties `Point $point` and `float $angle`.

### __construct()

```php
public function __construct(public Point $point, public float $angle)
```

Creates a line from a point and an angle.

- **Parameters:** `$point` a point on the line, `$angle` the angle in radians
- **Throws:** [NonFiniteValueException](errors.md) if the angle is `NAN` or infinite

### through()

```php
public static function through(Point $a, Point $b): self
```

Creates the line that passes through two points. The first point is stored as the point of the line. The check for identical points is exact, so points that are only very close together are accepted.

- **Parameters:** `$a` the first point, `$b` the second point
- **Returns:** `Line`
- **Throws:** [DegenerateLineException](errors.md) if both points have exactly the same coordinates

### isParallelTo()

```php
public function isParallelTo(Line $other, float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether two lines are parallel. The tolerance is compared to the sine of the angle between the lines. Identical lines and lines with opposite directions are parallel.

- **Parameters:** `$other` the other line, `$tolerance` the largest allowed sine of the angle between the lines
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

### intersectionWith()

```php
public function intersectionWith(Line $other, float $tolerance = Point::DEFAULT_TOLERANCE): ?Point
```

Returns the point where both lines meet. The lines count as parallel when the sine of the angle between them is at most the tolerance. The result is calculated from the direction vectors of the lines, so it is exact for axis aligned lines and for lines at multiples of 45 degrees through simple points, for example the x axis and the y axis meet at exactly `(0, 0)`.

- **Parameters:** `$other` the other line, `$tolerance` the largest allowed sine of the angle between the lines
- **Returns:** `Point` or `null` when the lines are parallel or identical
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite, [NonFiniteValueException](errors.md) if the crossing point overflows to infinity

### distanceTo()

```php
public function distanceTo(Point $point): float
```

Returns the perpendicular distance between the line and a point. The result is never negative.

- **Parameters:** `$point` the point to measure
- **Returns:** `float`

## Circle

An immutable circle. A radius of zero is allowed. It has the read-only properties `Point $center` and `float $radius`.

### __construct()

```php
public function __construct(public Point $center, public float $radius)
```

Creates a circle.

- **Parameters:** `$center` the center point, `$radius` the radius
- **Throws:** [NonFiniteValueException](errors.md) if the radius is `NAN` or infinite, [NegativeRadiusException](errors.md) if the radius is below zero

### area()

```php
public function area(): float
```

Returns the area, which is pi times the radius squared.

- **Returns:** `float`

### circumference()

```php
public function circumference(): float
```

Returns the length of the outline, which is 2 times pi times the radius.

- **Returns:** `float`

### contains()

```php
public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether a point is inside the circle or on its edge. The tolerance is added to the radius.

- **Parameters:** `$point` the point to test, `$tolerance` the extra distance allowed outside the edge
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

### intersectionsWith()

```php
public function intersectionsWith(Circle $other, float $tolerance = Point::DEFAULT_TOLERANCE): array
```

Returns the points shared by the outlines of both circles, as a `list<Point>` with none, one (tangent) or two points. Concentric circles, including identical ones, return an empty list. The tolerance is a distance: circles that miss each other (or lie inside each other) by at most that distance count as tangent, and centers at most that far apart count as concentric.

- **Parameters:** `$other` the other circle, `$tolerance` the largest allowed gap
- **Returns:** `list<Point>`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

## Rectangle

An immutable axis-aligned rectangle, defined by its minimum (bottom-left) corner and its size. It has the read-only properties `Point $origin`, `float $width` and `float $height`.

### __construct()

```php
public function __construct(public Point $origin, public float $width, public float $height)
```

Creates a rectangle. A width or height of zero is allowed.

- **Parameters:** `$origin` the corner with the smallest x and y, `$width` the size along the x axis, `$height` the size along the y axis
- **Throws:** [NonFiniteValueException](errors.md) if the width or height is `NAN` or infinite, [NegativeSizeException](errors.md) if the width or height is below zero

### fromCorners()

```php
public static function fromCorners(Point $a, Point $b): self
```

Creates the rectangle spanned by two opposite corners, given in any order.

- **Parameters:** `$a` one corner, `$b` the opposite corner
- **Returns:** `Rectangle`
- **Throws:** [NonFiniteValueException](errors.md) if the width or height overflows to infinity

### area()

```php
public function area(): float
```

Returns the width times the height.

- **Returns:** `float`

### perimeter()

```php
public function perimeter(): float
```

Returns the length of the outline.

- **Returns:** `float`

### contains()

```php
public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether a point is inside the rectangle or on its edge, or at most the tolerance outside it. The tolerance is the distance from the point to the rectangle, so beyond a corner it is measured diagonally.

- **Parameters:** `$point` the point to test, `$tolerance` the largest allowed distance outside the rectangle
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

### intersects()

```php
public function intersects(Rectangle $other): bool
```

Tests whether both rectangles share at least one point. Touching edges count.

- **Parameters:** `$other` the other rectangle
- **Returns:** `bool`

## Polygon

An immutable simple polygon, defined by its vertices in order. The closing side from the last point to the first is implicit. Self-intersecting polygons are not detected, and their results are not meaningful. It has the read-only property `list<Point> $points`.

### __construct()

```php
public function __construct(public array $points)
```

Creates a polygon. The array may have any keys, because the points are re-indexed with `array_values()` and stored as a `list<Point>`. Every value must be a `Point`.

- **Parameters:** `$points` the vertices in order, an array of `Point` objects
- **Throws:** [InvalidPointException](errors.md) if a value is not a `Point` (the message names its position), [TooFewPointsException](errors.md) if there are fewer than three points

### area()

```php
public function area(): float
```

Returns the absolute area, calculated with the shoelace formula. It does not depend on the orientation.

- **Returns:** `float`

### perimeter()

```php
public function perimeter(): float
```

Returns the total length of all sides, including the closing side.

- **Returns:** `float`

### centroid()

```php
public function centroid(): Point
```

Returns the center of mass of the polygon. The zero area check is exact, so a tiny but real polygon has a centroid. The calculation is relative to the first vertex. For a nearly flat polygon the result loses digits, see [Polygons](polygons.md) for when.

- **Returns:** `Point`
- **Throws:** [DegeneratePolygonException](errors.md) if the signed area is exactly zero

### contains()

```php
public function contains(Point $point, float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether a point is inside the polygon, using ray casting. Points on the boundary count as inside, and so do points at most the tolerance (a distance) outside an edge.

- **Parameters:** `$point` the point to test, `$tolerance` the largest allowed distance to an edge
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

### isConvex()

```php
public function isConvex(float $tolerance = Point::DEFAULT_TOLERANCE): bool
```

Tests whether all turns go the same way. Collinear vertices are allowed: a vertex counts as collinear when it lies at most the tolerance (a distance) from the line through the two vertices before it. A polygon without area is not convex.

- **Parameters:** `$tolerance` the largest distance that still counts as a straight line
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative, `NAN` or infinite

### orientation()

```php
public function orientation(): Orientation
```

Returns the winding direction of the points, assuming the y axis points up. The check for zero area is exact, so a tiny but real polygon still has an orientation.

- **Returns:** [Orientation](#orientation-1)

## Orientation

A pure enum, without backing values, with the winding direction of a polygon, assuming the y axis points up. It has no methods besides the standard enum ones (`cases()` and the `name` property).

### Clockwise

```php
case Clockwise;
```

The points go around clockwise. The signed area is negative.

- **Returns:** the enum case `Orientation::Clockwise`

### CounterClockwise

```php
case CounterClockwise;
```

The points go around counter-clockwise. The signed area is positive.

- **Returns:** the enum case `Orientation::CounterClockwise`

### Degenerate

```php
case Degenerate;
```

The polygon has exactly zero area (not just a very small one).

- **Returns:** the enum case `Orientation::Degenerate`
