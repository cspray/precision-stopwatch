<?php declare(strict_types=1);

namespace Cspray\PrecisionStopwatch;

use Cspray\PrecisionStopwatch\Exception\InvalidMarker;
use Cspray\PrecisionStopwatch\Exception\StopwatchAlreadyStarted;
use Cspray\PrecisionStopwatch\Exception\StopwatchNotStarted;

final class Stopwatch {

    private readonly PreciseTime $preciseTime;

    private bool $isRunning = false;

    /**
     * Ensures that marker IDs are unique-per-run.
     *
     * @var string
     */
    private string $markerIdSalt;

    private ?Marker $startMarker = null;

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
        $this->startMarker = null;
    }

    private function nextMarkerId() : string {
        return sprintf('%s_%s', $this->markerIdSalt, count($this->marks));
    }

    public function isRunning() : bool {
        return $this->isRunning;
    }

    /**
     * @throws StopwatchAlreadyStarted
     */
    public function start() : void {
        if ($this->isRunning()) {
            throw StopwatchAlreadyStarted::fromUnableToStartStartedStopwatch();
        }

        $this->isRunning = true;
        $this->startMarker = $this->mark();
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

        $start = isset($this->startMarker) ? $this->marks[$this->startMarker->getId()] : $now;

        return $this->createMarker($id, $start, $now);
    }

    /**
     * @throws StopwatchNotStarted
     */
    public function stop() : Metrics {
        if (!$this->isRunning()) {
            throw StopwatchNotStarted::fromUnableToStopNotRunningStopwatch();
        }

        $endMarker = $this->mark();
        $metrics = new class($this->marks, $this->startMarker, $endMarker) implements Metrics {

            /**
             * @param array<string, int|float> $marks
             */
            public function __construct(
                private readonly array $marks,
                private readonly Marker $start,
                private readonly Marker $end
            ) {}

            public function getTotalDuration() : Duration {
                return $this->getDurationBetweenMarkersById($this->start->getId(), $this->end->getId());
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

            public function getStartMarker() : Marker {
                return $this->start;
            }

            public function getEndMarker() : Marker {
                return $this->end;
            }
        };

        $this->setFreshMarkers();
        $this->isRunning = false;

        return $metrics;
    }

    private function createMarker(string $markerId, int|float $start, int|float $end) : Marker {
        return new class($markerId, new Duration($start, $end)) implements Marker {

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

}