<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry;

use PHPMath\Geometry\Exception\InvalidToleranceException;

use function is_finite;

/**
 * Validation of the absolute tolerance accepted by every comparing method.
 *
 * @internal
 */
final class Tolerance
{
    /** @throws InvalidToleranceException When the tolerance is negative, NAN or infinite. */
    public static function assertValid(float $tolerance): void
    {
        if (!is_finite($tolerance) || $tolerance < 0) {
            throw InvalidToleranceException::forValue($tolerance);
        }
    }
}
