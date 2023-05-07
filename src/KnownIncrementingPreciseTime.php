<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

final class KnownIncrementingPreciseTime implements PreciseTime {

    private int $incrementMultiplier = 0;

    public function __construct(
        private readonly int $incrementAmount = 1
    ) {}

    public function now() : int|float {
        return $this->incrementMultiplier++ * $this->incrementAmount;
    }

}