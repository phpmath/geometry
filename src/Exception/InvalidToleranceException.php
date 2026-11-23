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
 * Thrown when a tolerance is negative, NaN or infinite.
 */
final class InvalidToleranceException extends InvalidArgumentException implements GeometryException
{
    public static function forValue(float $value): self
    {
        return new self(sprintf(
            'The tolerance must be a finite number that is not negative, "%s" given.',
            var_export($value, true),
        ));
    }
}
