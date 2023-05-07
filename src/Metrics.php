<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

use Traversable;

interface Metrics {

    public function getTotalDuration() : Duration;

    public function getDurationBetweenMarkers(Marker $startMarker, Marker $endMarker) : Duration;

}
