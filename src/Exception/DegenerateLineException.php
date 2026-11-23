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
 * Thrown when a line is created through two identical points.
 */
final class DegenerateLineException extends InvalidArgumentException implements GeometryException
{
    public static function forIdenticalPoints(): self
    {
        return new self('A line requires two distinct points.');
    }
}
