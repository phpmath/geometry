<?php

/**
 * phpmath / geometry (https://github.com/phpmath/geometry)
 *
 * @link https://github.com/phpmath/geometry for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Geometry\Tests\Tolerance;

use PHPMath\Geometry\Exception\InvalidToleranceException;
use PHPMath\Geometry\Tolerance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Tolerance::class)]
#[CoversClass(InvalidToleranceException::class)]
#[CoversMethod(Tolerance::class, 'assertValid')]
final class AssertValidTest extends TestCase
{
    /** @return iterable<string, array{float}> */
    public static function validTolerances(): iterable
    {
        yield 'zero' => [0.0];
        yield 'default' => [1e-9];
        yield 'large' => [1e300];
    }

    /** @return iterable<string, array{float}> */
    public static function invalidTolerances(): iterable
    {
        yield 'negative' => [-1.0];
        yield 'NAN' => [NAN];
        yield 'infinite' => [INF];
    }

    #[DataProvider('validTolerances')]
    public function testAcceptsFiniteNonNegativeTolerance(float $tolerance): void
    {
        // Arrange
        $this->expectNotToPerformAssertions();

        // Act
        Tolerance::assertValid($tolerance);
    }

    #[DataProvider('invalidTolerances')]
    public function testThrowsOnInvalidTolerance(float $tolerance): void
    {
        // Arrange
        $this->expectException(InvalidToleranceException::class);

        // Act
        Tolerance::assertValid($tolerance);
    }

    public function testMessageNamesTheGivenValue(): void
    {
        // Arrange
        $this->expectExceptionMessage('The tolerance must be a finite number that is not negative, "-0.5" given.');

        // Act
        Tolerance::assertValid(-0.5);
    }
}
