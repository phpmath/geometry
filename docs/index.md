---
title: Introduction
intro: Immutable 2D geometry for PHP. Points, segments, lines, circles, rectangles and polygons are value objects with float coordinates.
group: Getting started
order: 10
---

## Requirements

The library has no runtime dependencies. It only needs a current PHP version.

- PHP 8.4 or newer

## Installation

Install the library with Composer.

```bash
composer require phpmath/geometry
```

## A first example

Create two points, measure the distance between them and find the middle. Coordinates are floats, so integers are converted for you.

```php
use PHPMath\Geometry\Point;

$a = new Point(0, 0);
$b = new Point(3, 4);

echo $a->distanceTo($b); // 5

$middle = $a->midpointTo($b);

echo $middle->x; // 1.5
echo $middle->y; // 2
```

Calling `midpointTo()` did not change `$a` or `$b`. It returned a new `Point`. This is true for every operation in the library.

## Where to go next

Start with the core concepts, or jump straight to the shapes you need.

<div class="cards"><a href="concepts.md"><strong>Core concepts</strong><span>Immutability, creating shapes and reading values.</span></a><a href="points-and-segments.md"><strong>Points and segments</strong><span>Distance, midpoints, tolerance and segment intersections.</span></a><a href="lines.md"><strong>Lines</strong><span>Infinite lines, angles, parallels and intersections.</span></a><a href="circles-and-rectangles.md"><strong>Circles and rectangles</strong><span>Area, containment and intersections.</span></a><a href="polygons.md"><strong>Polygons</strong><span>Area, centroid, convexity and orientation.</span></a><a href="api.md"><strong>API reference</strong><span>Every class and method with its parameters and errors.</span></a></div>
