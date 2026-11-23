<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Exception;

use InvalidArgumentException;
use PHPMath\Geometry\Point;

use function get_debug_type;
use function sprintf;

/**
 * Thrown when a polygon is created with a value that is not a Point.
 */
final class InvalidPointException extends InvalidArgumentException implements GeometryException
{
    public static function forPosition(int $position, mixed $value): self
    {
        return new self(sprintf(
            'The polygon point at position %d must be an instance of %s, "%s" given.',
            $position,
            Point::class,
            get_debug_type($value),
        ));
    }
}
