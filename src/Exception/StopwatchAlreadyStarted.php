<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Exception;

final class StopwatchAlreadyStarted extends Exception {

    public static function fromUnableToStartStartedStopwatch() : self {
        return new self('Unable to start a Stopwatch that is currently running.');
    }

}