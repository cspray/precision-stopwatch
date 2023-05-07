<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

final class Duration {

    private readonly int|float $timeTakenInNanoseconds;

    public function __construct(
        private readonly int|float $start,
        private readonly int|float $end
    ) {
        $this->timeTakenInNanoseconds = $this->end - $this->start;
    }

    public function timeTakenInNanoseconds() : int|float {
        return $this->timeTakenInNanoseconds;
    }

    public function timeTakenInMilliseconds() : int|float {
        return $this->timeTakenInNanoseconds / 1_000_000;
    }

}