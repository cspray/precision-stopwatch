<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

interface Marker {

    public function getId() : string;

    public function getDuration() : Duration;

}
