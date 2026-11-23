<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Exception;

use DomainException;

/**
 * Thrown when an operation is not defined for a polygon without area.
 */
final class DegeneratePolygonException extends DomainException implements GeometryException
{
    public static function forZeroArea(): self
    {
        return new self('The operation is not defined for a polygon with zero area.');
    }
}
