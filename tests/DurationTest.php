<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Test;

use Cspray\PrecisionStopwatch\Duration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Duration::class)]
final class DurationTest extends TestCase {

    public function testTimeTakenInNanosecondsReturnsExpectedResult() : void {
        $subject = new Duration(0, 1_000_000);

        self::assertSame(1_000_000, $subject->timeTakenInNanoseconds());
    }

    public function testTimeTakenInMillisecondsReturnsExpectedResult() : void {
        $subject = new Duration(0, 1_000_000);

        self::assertSame(1, $subject->timeTakenInMilliseconds());
    }

}