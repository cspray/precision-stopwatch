<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

/**
 *
 */
interface PreciseTime {

    /**
     *
     *
     * @return int|float
     */
    public function now() : int|float;

}