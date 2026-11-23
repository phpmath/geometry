---
title: Lines
intro: Work with infinite lines, their angles, parallels and intersections.
group: Guides
order: 40
---

## Defining a line

A `Line` goes on forever in both directions. It is defined by one point on the line and an angle in radians, measured counter-clockwise from the positive x axis. An angle of `0` is a horizontal line and `M_PI / 2` is a vertical line.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$horizontal = new Line(new Point(0, 2), 0);
$vertical = new Line(new Point(5, 0), M_PI / 2);

echo $horizontal->angle; // 0
```

An angle that is `NAN` or infinite throws [`NonFiniteValueException`](errors.md).

## A line through two points

`Line::through()` builds the line that passes through two points. The first point is stored and the angle is calculated from the direction of the second point.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$line = Line::through(new Point(0, 0), new Point(1, 1));

echo $line->angle; // 0.78539816339745

$left = Line::through(new Point(0, 0), new Point(-1, 0));

echo $left->angle; // 3.1415926535898
```

The angle is between `-M_PI` and `M_PI`. Two identical points do not define a line, so they throw [`DegenerateLineException`](errors.md). This check is exact, with no tolerance: points that are only very close together still define a line.

```php
use PHPMath\Geometry\Exception\DegenerateLineException;
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

try {
    Line::through(new Point(2, 2), new Point(2, 2));
} catch (DegenerateLineException $exception) {
    echo $exception->getMessage(); // A line requires two distinct points.
}
```

## Distance to a point

`distanceTo()` returns the shortest distance between the line and a point, which is the length of the perpendicular. The distance is never negative.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$line = Line::through(new Point(0, 0), new Point(3, 4));

echo $line->distanceTo(new Point(4, 3)); // 1.4

$horizontal = new Line(new Point(0, 2), 0);

echo $horizontal->distanceTo(new Point(7, 5)); // 3
```

## Parallel lines

Two lines are parallel when they point in the same direction, or in exactly opposite directions. `isParallelTo()` tests this within a tolerance. The tolerance is compared to the sine of the angle between the lines, so for small angles it is roughly the angle in radians.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$horizontal = new Line(new Point(0, 2), 0);

var_dump($horizontal->isParallelTo(new Line(new Point(0, 5), 0)));         // bool(true)
var_dump($horizontal->isParallelTo(new Line(new Point(0, 0), M_PI)));      // bool(true)
var_dump($horizontal->isParallelTo(new Line(new Point(5, 0), M_PI / 2)));  // bool(false)
var_dump($horizontal->isParallelTo(new Line(new Point(0, 0), 1e-10)));     // bool(true)
var_dump($horizontal->isParallelTo(new Line(new Point(0, 0), 1e-8)));      // bool(false)
```

Identical lines also count as parallel. A negative, `NAN` or infinite tolerance throws [`InvalidToleranceException`](errors.md). Unlike the other methods, this tolerance is not a distance but the sine of an angle, because lines have no length. See [Core concepts](concepts.md).

## Intersecting lines

`intersectionWith()` returns the point where two lines cross. Lines that are parallel, including identical lines, have no single crossing, so the result is `null`. Lines count as parallel when the sine of the angle between them is at most the optional tolerance, the same as `isParallelTo()`.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$horizontal = new Line(new Point(0, 2), 0);
$vertical = new Line(new Point(5, 0), M_PI / 2);

$cross = $horizontal->intersectionWith($vertical);

echo $cross->x, ', ', $cross->y; // 5, 2

var_dump($horizontal->intersectionWith(new Line(new Point(0, 5), 0))); // NULL
```

Two diagonal lines show the typical use.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$diagonal = Line::through(new Point(0, 0), new Point(1, 1));
$other = Line::through(new Point(0, 4), new Point(4, 0));

$cross = $diagonal->intersectionWith($other);

echo $cross->x, ', ', $cross->y; // 2, 2
```

The crossing is calculated from the direction vectors of the lines. For axis aligned lines (angles that are a multiple of `M_PI / 2`) and for diagonals (a multiple of `M_PI / 4`) the direction is exact, so the result is exact too. The x axis and the y axis meet at exactly `(0, 0)`, and so does the line `y = x` with the x axis.

```php
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;

$xAxis = new Line(new Point(0, 0), 0);
$yAxis = new Line(new Point(0, 1), M_PI / 2);
$diagonal = Line::through(new Point(0, 0), new Point(1, 1));

$origin = $xAxis->intersectionWith($yAxis);
$other = $diagonal->intersectionWith($xAxis);

var_dump($origin->x, $origin->y); // float(0) float(0)
var_dump($other->x, $other->y);   // float(0) float(0)
```

> [!WARNING]
> Exactness only holds for those angles, within a few units of float rounding of the angle. A line at any other angle, for example `0.3` radians, uses sine and cosine, so its results can carry tiny float errors (around `1E-16` relative). Compare such points with `equals()` instead of `===`. Lines that are almost parallel but outside the tolerance give a crossing very far away.
