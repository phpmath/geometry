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
 * Thrown when a polygon is created with fewer than three points.
 */
final class TooFewPointsException extends InvalidArgumentException implements GeometryException
{
    public static function forCount(int $count): self
    {
        return new self(sprintf('A polygon requires at least 3 points, %d given.', $count));
    }
}
