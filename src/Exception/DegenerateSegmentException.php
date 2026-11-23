<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Exception;

use InvalidArgumentException;

/**
 * Thrown when a segment is created with identical start and end points.
 */
final class DegenerateSegmentException extends InvalidArgumentException implements GeometryException
{
    public static function forIdenticalPoints(): self
    {
        return new self('A segment requires two distinct points.');
    }
}
