<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Exception;

use InvalidArgumentException;

use function sprintf;

/**
 * Thrown when a circle is created with a negative radius.
 */
final class NegativeRadiusException extends InvalidArgumentException implements GeometryException
{
    public static function forRadius(float $radius): self
    {
        return new self(sprintf('The radius must not be negative, "%s" given.', (string)$radius));
    }
}
