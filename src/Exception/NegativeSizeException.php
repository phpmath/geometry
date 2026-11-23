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
 * Thrown when a rectangle is created with a negative width or height.
 */
final class NegativeSizeException extends InvalidArgumentException implements GeometryException
{
    public static function forSize(float $width, float $height): self
    {
        return new self(
            sprintf('The width and height must not be negative, "%s" and "%s" given.', (string)$width, (string)$height),
        );
    }
}
