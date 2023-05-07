<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

use Cspray\PrecisionStopwatch\Exception\InvalidMarker;
use Cspray\PrecisionStopwatch\Exception\StopwatchAlreadyStarted;
use Cspray\PrecisionStopwatch\Exception\StopwatchNotStarted;

final class Stopwatch {

    private readonly PreciseTime $preciseTime;

    /**
     * Ensures that marker IDs are unique-per-run.
     *
     * @var string
     */
    private string $markerIdSalt;

    /**
     * Keeps up with the marker ID for the call to Stopwatch::start() so that we don't have to generate the ID while
     * the stopwatch is running.
     *
     * @var string
     */
    private string $startId;

    /**
     * @var array<string, int|float>
     */
    private array $marks;

    public function __construct(
        PreciseTime $preciseTime = null
    ) {
        $this->preciseTime = $preciseTime ?? new HighResolutionPreciseTime();
        $this->setFreshMarkers();
    }

    private function setFreshMarkers() : void {
        $this->marks = [];
        $this->markerIdSalt = bin2hex(random_bytes(8));
        $this->startId = $this->nextMarkerId();
    }

    private function nextMarkerId() : string {
        return sprintf('%s_%s', $this->markerIdSalt, count($this->marks));
    }

    public function isRunning() : bool {
        return array_key_exists($this->startId, $this->marks);
    }

    /**
     * @throws StopwatchAlreadyStarted
     */
    public function start() : void {
        if ($this->isRunning()) {
            throw StopwatchAlreadyStarted::fromUnableToStartStartedStopwatch();
        }

        $this->marks[$this->startId] = $this->preciseTime->now();
    }

    /**
     * @throws StopwatchNotStarted
     */
    public function mark() : Marker {
        if (!$this->isRunning()) {
            throw StopwatchNotStarted::fromUnableToMarkNotRunningStopwatch();
        }

        $now = $this->preciseTime->now();
        $id = $this->nextMarkerId();
        $this->marks[$id] = $now;

        return new class($id, new Duration($this->marks[$this->startId], $now)) implements Marker {

            public function __construct(
                private readonly string $id,
                private readonly Duration $duration,
            ) {}

            public function getId() : string {
                return $this->id;
            }

            public function getDuration() : Duration {
                return $this->duration;
            }
        };
    }

    /**
     * @throws StopwatchNotStarted
     */
    public function stop() : Metrics {
        if (!$this->isRunning()) {
            throw StopwatchNotStarted::fromUnableToStopNotRunningStopwatch();
        }

        $now = $this->preciseTime->now();
        $endId = $this->nextMarkerId();
        $this->marks[$endId] = $now;

        $metrics = new class($this->marks, $this->startId, $endId) implements Metrics {

            /**
             * @param array<string, int|float> $marks
             */
            public function __construct(
                private readonly array $marks,
                private readonly string $startId,
                private readonly string $endId
            ) {}

            public function getTotalDuration() : Duration {
                return $this->getDurationBetweenMarkersById($this->startId, $this->endId);
            }

            public function getDurationBetweenMarkers(Marker $startMarker, Marker $endMarker) : Duration {
                return $this->getDurationBetweenMarkersById($startMarker->getId(), $endMarker->getId());
            }

            private function getDurationBetweenMarkersById(string $start, string $end) : Duration {
                if (!array_key_exists($start, $this->marks)) {
                    throw InvalidMarker::fromMarkerNotPresent();
                }
                if (!array_key_exists($end, $this->marks)) {
                    throw InvalidMarker::fromMarkerNotPresent();
                }

                return new Duration($this->marks[$start], $this->marks[$end]);
            }
        };

        $this->setFreshMarkers();

        return $metrics;
    }

}