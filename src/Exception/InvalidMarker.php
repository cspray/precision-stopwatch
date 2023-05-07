<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch\Exception;

final class InvalidMarker extends Exception {

    public static function fromMarkerNotPresent() : self {
        return new self('Attempted to get a duration for an invalid marker.');
    }

}
