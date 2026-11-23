---
title: Circles and rectangles
intro: Measure circles and axis-aligned rectangles, test containment and find intersections.
group: Guides
order: 50
---

## Circles

A `Circle` has a center point and a radius. A radius of zero is allowed and gives a circle that is a single point. A negative radius throws [`NegativeRadiusException`](errors.md), and a `NAN` or infinite radius throws [`NonFiniteValueException`](errors.md).

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;

$circle = new Circle(new Point(0, 0), 5);

echo $circle->area();          // 78.539816339745
echo $circle->circumference(); // 31.415926535898
```

## Does a point lie in a circle

`contains()` tests whether a point is inside the circle or on its edge. The tolerance is added to the radius, so a point slightly outside still counts. It is an absolute value, see [Core concepts](concepts.md) for the policy shared by all methods.

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;

$circle = new Circle(new Point(0, 0), 5);

var_dump($circle->contains(new Point(3, 4)));                // bool(true)
var_dump($circle->contains(new Point(4, 4)));                // bool(false)
var_dump($circle->contains(new Point(0, 5.0000000001)));     // bool(true)
var_dump($circle->contains(new Point(0, 5.1), 0.2));         // bool(true)
```

The point `(3, 4)` is exactly 5 away from the center, so it is on the edge and counts as inside. The point `(4, 4)` is about 5.66 away. The third point is outside by `1.0E-10`, which is within the default tolerance.

## Intersecting circles

`intersectionsWith()` returns the points where the outlines of two circles meet, as a list. The list has two points when the circles cross, one point when they touch (are tangent) and no points otherwise.

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;

$circle = new Circle(new Point(0, 0), 5);

$cross = $circle->intersectionsWith(new Circle(new Point(6, 0), 5));

echo count($cross);                // 2
echo $cross[0]->x, ', ', $cross[0]->y; // 3, 4
echo $cross[1]->x, ', ', $cross[1]->y; // 3, -4

$touch = $circle->intersectionsWith(new Circle(new Point(10, 0), 5));

echo count($touch);                // 1
echo $touch[0]->x, ', ', $touch[0]->y; // 5, 0
```

The list is empty when the circles are too far apart, when one circle lies inside the other without touching, and when the circles are concentric (share a center). Identical circles are concentric, so they also return an empty list, even though every point is shared.

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;

$circle = new Circle(new Point(0, 0), 5);

echo count($circle->intersectionsWith(new Circle(new Point(20, 0), 5))); // 0
echo count($circle->intersectionsWith(new Circle(new Point(1, 0), 1)));  // 0
echo count($circle->intersectionsWith($circle));                         // 0
```

`intersectionsWith()` takes an optional tolerance, a distance. Circles that miss each other (or lie inside each other) by at most the tolerance count as touching, and centers at most that far apart count as concentric. The default is `Point::DEFAULT_TOLERANCE`.

```php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Point;

$circle = new Circle(new Point(0, 0), 5);
$near = new Circle(new Point(10.05, 0), 5);

echo count($circle->intersectionsWith($near));       // 0
echo count($circle->intersectionsWith($near, 0.1));  // 1
```

An invalid tolerance (negative, `NAN` or infinite) throws [`InvalidToleranceException`](errors.md).

## Rectangles

A `Rectangle` is axis-aligned, so its sides are parallel to the x and y axes. It is defined by its origin, the corner with the smallest x and y (bottom-left), and by a width and a height. A width or height of zero is allowed. A negative size throws [`NegativeSizeException`](errors.md), and a `NAN` or infinite size throws [`NonFiniteValueException`](errors.md).

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;

$rectangle = new Rectangle(new Point(1, 1), 4, 3);

echo $rectangle->area();      // 12
echo $rectangle->perimeter(); // 14
```

## From two corners

`Rectangle::fromCorners()` builds the rectangle between two opposite corners. The corners can be given in any order, and the result always has the smallest corner as its origin.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;

$one = Rectangle::fromCorners(new Point(5, 4), new Point(1, 1));
$two = Rectangle::fromCorners(new Point(1, 4), new Point(5, 1));

echo $one->origin->x, ', ', $one->origin->y, ', ', $one->width, ', ', $one->height; // 1, 1, 4, 3
echo $two->origin->x, ', ', $two->origin->y, ', ', $two->width, ', ', $two->height; // 1, 1, 4, 3
```

## Does a point lie in a rectangle

`contains()` tests whether a point is inside the rectangle or on its edge. Like the other methods it takes an optional tolerance: a point at most that distance outside the rectangle still counts. The distance is measured to the rectangle, so beyond a corner it is diagonal. Pass `0.0` for an exact test.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;

$rectangle = new Rectangle(new Point(1, 1), 4, 3);

var_dump($rectangle->contains(new Point(2, 2)));   // bool(true)
var_dump($rectangle->contains(new Point(5, 4)));   // bool(true)
var_dump($rectangle->contains(new Point(5.1, 4))); // bool(false)
var_dump($rectangle->contains(new Point(5.1, 4), 0.2)); // bool(true)
var_dump($rectangle->contains(new Point(5.00000000001, 4), 0.0)); // bool(false)
```

The last example is outside by `1.0E-11`. That is within the default tolerance, but not with a tolerance of `0.0`.

## Do two rectangles overlap

`intersects()` tests whether two rectangles share at least one point. Rectangles that only touch along an edge or at a corner count, and so does a rectangle that lies completely inside another one. The method does not return the overlapping area, only a yes or no.

```php
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Rectangle;

$rectangle = new Rectangle(new Point(1, 1), 4, 3);

var_dump($rectangle->intersects(new Rectangle(new Point(4, 3), 3, 3))); // bool(true)
var_dump($rectangle->intersects(new Rectangle(new Point(5, 1), 2, 2))); // bool(true)
var_dump($rectangle->intersects(new Rectangle(new Point(6, 1), 2, 2))); // bool(false)
var_dump($rectangle->intersects(new Rectangle(new Point(2, 2), 1, 1))); // bool(true)
```

The second rectangle only shares the edge at `x = 5`. The last one lies inside the first.
