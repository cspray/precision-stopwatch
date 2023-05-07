<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

use Cspray\PrecisionStopwatch\Exception\HighResolutionTimeNotSupported;

final class HighResolutionPreciseTime implements PreciseTime {

    /**
     * @throws HighResolutionTimeNotSupported
     */
    public function now() : int|float {
        $now = hrtime(true);
        if ($now === false) {
            throw HighResolutionTimeNotSupported::fromHrtimeNotSupported();
        }

        return $now;
    }

}
