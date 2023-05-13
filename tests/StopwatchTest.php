<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Test;

use Cspray\PrecisionStopwatch\Duration;
use Cspray\PrecisionStopwatch\Exception\Exception;
use Cspray\PrecisionStopwatch\Exception\InvalidMarker;
use Cspray\PrecisionStopwatch\Exception\StopwatchAlreadyStarted;
use Cspray\PrecisionStopwatch\Exception\StopwatchNotStarted;
use Cspray\PrecisionStopwatch\KnownIncrementingPreciseTime;
use Cspray\PrecisionStopwatch\Stopwatch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[
    CoversClass(Stopwatch::class),
    CoversClass(KnownIncrementingPreciseTime::class),
    CoversClass(Duration::class),
    CoversClass(Exception::class),
    CoversClass(InvalidMarker::class),
    CoversClass(StopwatchNotStarted::class),
    CoversClass(StopwatchAlreadyStarted::class),
]
final class StopwatchTest extends TestCase {

    private Stopwatch $subject;

    protected function setUp() : void {
        $this->subject = new Stopwatch(new KnownIncrementingPreciseTime());
    }

    public function testStopwatchWithNoMarksHasCorrectTotalDuration() : void {
        $this->subject->start();
        $metrics = $this->subject->stop();

        $duration = $metrics->getTotalDuration();

        self::assertSame(1, $duration->timeTakenInNanoseconds());
    }

    public function testIsRunningReturnsFalseWhenStopwatchNotStarted() : void {
        self::assertFalse($this->subject->isRunning());
    }

    public function testIsRunningAfterStarted() : void {
        $this->subject->start();
        self::assertTrue($this->subject->isRunning());
    }

    public function testIsRunningAfterStartedAndStopped() : void {
        $this->subject->start();
        $this->subject->stop();

        self::assertFalse($this->subject->isRunning());
    }

    public function testSeveralMarksHasCorrectDurationUpToMark() : void {
        $this->subject->start();
        $mark1 = $this->subject->mark();
        $mark2 = $this->subject->mark();
        $mark3 = $this->subject->mark();
        $metrics = $this->subject->stop();

        self::assertSame(4, $metrics->getTotalDuration()->timeTakenInNanoseconds());
        self::assertSame(1, $mark1->getDuration()->timeTakenInNanoseconds());
        self::assertSame(2, $mark2->getDuration()->timeTakenInNanoseconds());
        self::assertSame(3, $mark3->getDuration()->timeTakenInNanoseconds());
    }

    public function testGetDurationBetweenMarkers() : void {
        $this->subject->start();
        $mark1 = $this->subject->mark();
        $mark2 = $this->subject->mark();
        $mark3 = $this->subject->mark();
        $metrics = $this->subject->stop();

        self::assertSame(1, $metrics->getDurationBetweenMarkers($mark1, $mark2)->timeTakenInNanoseconds());
        self::assertSame(2, $metrics->getDurationBetweenMarkers($mark1, $mark3)->timeTakenInNanoseconds());
        self::assertSame(1, $metrics->getDurationBetweenMarkers($mark2, $mark3)->timeTakenInNanoseconds());
    }

    public function testStopwatchNotStartedThrowsExceptionOnMark() : void {
        $this->expectException(StopwatchNotStarted::class);
        $this->expectExceptionMessage('Unable to mark a Stopwatch that is not running.');

        $this->subject->mark();
    }

    public function testStopwatchStartedThrowsExceptionIfStartedAgain() : void {
        $this->subject->start();

        $this->expectException(StopwatchAlreadyStarted::class);
        $this->expectExceptionMessage('Unable to start a Stopwatch that is currently running.');

        $this->subject->start();
    }

    public function testStopwatchStoppedBeforeStartedThrowsException() : void {
        $this->expectException(StopwatchNotStarted::class);
        $this->expectExceptionMessage('Unable to stop a Stopwatch that is not running.');

        $this->subject->stop();
    }

    public function testMetricsGetDurationBetweenMarkersStartNotPresentThrowsException() : void {
        $this->subject->start();
        $goodMarker = $this->subject->mark();
        $metrics = $this->subject->stop();

        $differentWatch = new Stopwatch(new KnownIncrementingPreciseTime());
        $differentWatch->start();
        $marker = $differentWatch->mark();
        $differentWatch->stop();

        $this->expectException(InvalidMarker::class);
        $this->expectExceptionMessage('Attempted to get a duration for an invalid marker.');

        $metrics->getDurationBetweenMarkers($marker, $goodMarker);
    }

    public function testMetricsGetDurationBetweenMarkersEndNotPresentThrowsException() : void {
        $this->subject->start();
        $goodMarker = $this->subject->mark();
        $metrics = $this->subject->stop();

        $differentWatch = new Stopwatch(new KnownIncrementingPreciseTime());
        $differentWatch->start();
        $marker = $differentWatch->mark();
        $differentWatch->stop();

        $this->expectException(InvalidMarker::class);
        $this->expectExceptionMessage('Attempted to get a duration for an invalid marker.');

        $metrics->getDurationBetweenMarkers($goodMarker, $marker);
    }

    public function testMarkHasCorrectDuration() : void {
        $this->subject->start();
        $mark1 = $this->subject->mark();
        $this->subject->stop();

        self::assertSame(1, $mark1->getDuration()->timeTakenInNanoseconds());
    }

    public function testRestartingStoppedWatchReturnsUniqueMarkerIds() : void {
        $this->subject->start();
        $mark1 = $this->subject->mark();
        $this->subject->stop();

        $this->subject->start();
        $mark2 = $this->subject->mark();
        $this->subject->stop();

        self::assertNotSame($mark1->getId(), $mark2->getId());
    }

    public function testGettingStartMarkerReturnsDurationWithZero() : void {
        $this->subject->start();

        $metrics = $this->subject->stop();

        self::assertSame(0, $metrics->getStartMarker()->getDuration()->timeTakenInNanoseconds());
    }

    public function testGettingEndMarkerReturnsDurationMarkerMatchingTotalDuration() : void {
        $this->subject->start();

        $metrics = $this->subject->stop();

        self::assertSame(
            $metrics->getTotalDuration()->timeTakenInNanoseconds(),
            $metrics->getEndMarker()->getDuration()->timeTakenInNanoseconds()
        );
    }

}
