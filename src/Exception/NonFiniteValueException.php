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
use function var_export;

/**
 * Thrown when a coordinate, angle or measurement is NaN or infinite.
 */
final class NonFiniteValueException extends InvalidArgumentException implements GeometryException
{
    public static function forValue(string $name, float $value): self
    {
        return new self(sprintf('The %s must be a finite number, "%s" given.', $name, var_export($value, true)));
    }
}
