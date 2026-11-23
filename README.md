# geometry

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![CI][ico-ci]][link-ci]
[![Total Downloads][ico-downloads]][link-downloads]

A PHP library to work with immutable 2D geometry.

## Documentation

The full documentation is at https://phpmath.github.io/geometry/. Every change is listed in the [changelog](CHANGELOG.md) and every release on the [releases page](https://github.com/phpmath/geometry/releases).

## Install

```bash
composer require phpmath/geometry
```

## Requirements

- PHP 8.4 or newer

## Usage

Every shape is an immutable value object with float coordinates. Operations never change the instance they
are called on, they return a new instance. Invalid input is rejected with a specific exception from the
`PHPMath\Geometry\Exception` namespace.

``` php
use PHPMath\Geometry\Circle;
use PHPMath\Geometry\Line;
use PHPMath\Geometry\Point;
use PHPMath\Geometry\Polygon;
use PHPMath\Geometry\Segment;

$a = new Point(0, 0);
$b = new Point(3, 4);

$a->distanceTo($b);                          // 5.0
$a->midpointTo($b);                          // Point(1.5, 2.0)

$segment = new Segment($a, $b);
$segment->intersectionWith(new Segment(new Point(0, 4), new Point(3, 0))); // Point(1.5, 2.0)

$line = Line::through($a, $b);
$line->distanceTo(new Point(4, 3));          // 0.8 (perpendicular distance)

$circle = new Circle(new Point(0, 0), 5);
$circle->intersectionsWith(new Circle(new Point(6, 0), 5)); // [Point(3, 4), Point(3, -4)]

$polygon = new Polygon([new Point(0, 0), new Point(4, 0), new Point(4, 4), new Point(0, 4)]);
$polygon->area();                            // 16.0
$polygon->centroid();                        // Point(2.0, 2.0)
$polygon->contains(new Point(1, 1));         // true
```

## Features

* `Point`: `distanceTo()`, `midpointTo()`, `translate()` and `equals()` with a tolerance.
* `Segment`: `length()`, `midpoint()`, `intersectionWith()` and `contains()`.
* `Line` (through two points with `Line::through()`, or from a point and an angle): `intersectionWith()`,
  `distanceTo()` and `isParallelTo()`.
* `Circle`: `area()`, `circumference()`, `contains()` and `intersectionsWith()`.
* `Rectangle` (axis-aligned, also `Rectangle::fromCorners()`): `area()`, `perimeter()`, `contains()` and
  `intersects()`.
* `Polygon` (at least three points): `area()` (shoelace), `perimeter()`, `centroid()`, `contains()` (ray casting),
  `isConvex()` and `orientation()`.
* Exceptions for negative radius or size, non-finite values, degenerate segments and lines, and polygons with too
  few points.

Tolerance based methods default to `Point::DEFAULT_TOLERANCE` (`1e-9`). Boundaries are inclusive: a point on a
circle, rectangle or polygon edge is contained. Angles are in radians, counter-clockwise from the positive x axis.

## Development

```bash
composer test
composer test-coverage
composer analyse
composer check-style
composer fix-style
```

## Contributing

All contributions are welcome. Open an issue or a pull request on GitHub. Please follow the coding standard and add tests.

## Security

If you discover a security issue, please create an issue in the issue tracker.

## Credits

- [Walter Tamboer](https://github.com/waltertamboer)
- [All contributors](https://github.com/phpmath/geometry/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/phpmath/geometry.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ci]: https://img.shields.io/github/actions/workflow/status/phpmath/geometry/ci.yml?branch=master&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/phpmath/geometry.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/phpmath/geometry
[link-ci]: https://github.com/phpmath/geometry/actions/workflows/ci.yml
[link-downloads]: https://packagist.org/packages/phpmath/geometry/stats
