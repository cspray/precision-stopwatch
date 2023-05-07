<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Exception;

final class HighResolutionTimeNotSupported extends Exception {

    public static function fromHrtimeNotSupported() : self {
        return new self('The timing function hrtime() returned a failure. Please contact your system administrator.');
    }

}