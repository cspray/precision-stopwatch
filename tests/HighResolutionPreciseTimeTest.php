<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Test;

use Cspray\PrecisionStopwatch\HighResolutionPreciseTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighResolutionPreciseTime::class)]
final class HighResolutionPreciseTimeTest extends TestCase {

    public function testHighResolutionPreciseTimeReturnsNumber() : void {
        $subject = new HighResolutionPreciseTime();

        self::assertIsNumeric($subject->now());
    }

}