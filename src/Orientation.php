<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

/**
 * The winding direction of a polygon, assuming the y axis points up.
 */
enum Orientation
{
    case Clockwise;
    case CounterClockwise;
    case Degenerate;
}
