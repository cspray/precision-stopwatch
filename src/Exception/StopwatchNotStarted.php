<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Exception;

final class StopwatchNotStarted extends Exception {

    public static function fromUnableToMarkNotRunningStopwatch() : self {
        return new self('Unable to mark a Stopwatch that is not running.');
    }

    public static function fromUnableToStopNotRunningStopwatch() : self {
        return new self('Unable to stop a Stopwatch that is not running.');
    }
}